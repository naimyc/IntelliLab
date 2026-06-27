<?php

namespace App\Http\Controllers;

use App\Services\GptService;
use Illuminate\Http\Request;
use RuntimeException;

class ChatController extends Controller
{
    public function __construct(private GptService $gpt)
    {
    }

    /**
     * Plain pass-through chat (kept for the existing ChatView).
     * Returns the original choices shape so the frontend keeps working.
     */
    public function chat(Request $request)
    {
        $request->validate(['prompt' => 'required|string']);

        $response = $this->gpt->chat(
            $this->gpt->buildMessages($request->prompt)
        );

        return response()->json($response->json(), $response->status());
    }

    /**
     * Generate a structured Codecademy-style exercise from a topic/text.
     */
    public function generate(Request $request)
    {
        $request->validate(['topic' => 'required|string']);

        try {
            $exercise = $this->gpt->askJson(
                prompt: "Topic or text:\n\"\"\"\n{$request->topic}\n\"\"\"",
                system: $this->exerciseSystemPrompt(),
                options: ['temperature' => 0.4],
            );
        } catch (RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json($exercise);
    }

    private function exerciseSystemPrompt(): string
    {
        return <<<'PROMPT'
You are a programming exercise generator.
From the given topic/text, create a structured learning exercise similar to Codecademy.
Return ONLY valid JSON (no markdown, no explanation).
The JSON must follow this structure:
{
  "title": "string",
  "topic": "string",
  "difficulty": "easy | medium | hard",
  "explanation": "short explanation of the concept",
  "steps": ["step 1 explanation","step 2 explanation","step 3 explanation"],
  "exercise": { "question": "clear programming task for the user", "starter_code": "code skeleton if applicable" },
  "solution": { "final_code": "correct full solution", "explanation": "step-by-step explanation of the solution" },
  "test_cases": [ { "input": "", "expected_output": "" } ]
}
Rules:
- Always return valid JSON
- No markdown, no backticks
- No extra text outside JSON
- Make it beginner-friendly unless specified otherwise
- Include real-world examples if possible
- If code is required, prefer JavaScript or Python (choose one)
- Ensure the exercise matches the given topic exactly
PROMPT;
    }
}