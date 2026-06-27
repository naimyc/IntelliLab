<?php

namespace App\Http\Controllers;

use App\Services\GptService;
use Illuminate\Http\Request;
use RuntimeException;

/**
 * Generic, reusable GPT endpoint.
 *
 * Accepts any of:
 *   { "prompt": "..." }
 *   { "prompt": "...", "system": "..." }
 *   { "messages": [ { "role": "...", "content": "..." }, ... ] }
 *
 * Optional: model, temperature, max_tokens, json (bool).
 * With "json": true the reply is parsed and returned as the JSON object;
 * otherwise you get { "content": "..." }.
 */
class GptController extends Controller
{
    public function __construct(private GptService $gpt)
    {
    }

    public function generate(Request $request)
    {
        $data = $request->validate([
            'prompt'             => 'required_without:messages|string',
            'system'             => 'nullable|string',
            'messages'           => 'sometimes|array|min:1',
            'messages.*.role'    => 'required_with:messages|string|in:system,user,assistant',
            'messages.*.content' => 'required_with:messages|string',
            'model'              => 'nullable|string',
            'temperature'        => 'nullable|numeric|min:0|max:2',
            'max_tokens'         => 'nullable|integer|min:1',
            'json'               => 'nullable|boolean',
        ]);

        $messages = $data['messages']
            ?? $this->gpt->buildMessages($data['prompt'], $data['system'] ?? null);

        $options = array_filter([
            'model'       => $data['model'] ?? null,
            'temperature' => $data['temperature'] ?? null,
            'max_tokens'  => $data['max_tokens'] ?? null,
            'json'        => $data['json'] ?? null,
        ], fn ($v) => $v !== null);

        $response = $this->gpt->chat($messages, $options);

        if ($response->failed()) {
            return response()->json([
                'error'   => 'KIConnect request failed',
                'details' => $response->json() ?? $response->body(),
            ], $response->status() ?: 502);
        }

        $content = (string) data_get($response->json(), 'choices.0.message.content', '');

        // JSON mode: parse and return the object directly.
        if (!empty($data['json'])) {
            $parsed = $this->gpt->extractJson($content);

            if ($parsed === null) {
                return response()->json([
                    'error' => 'Model did not return valid JSON',
                    'raw'   => $content,
                ], 422);
            }

            return response()->json($parsed);
        }

        // Plain mode: return the text.
        return response()->json(['content' => $content]);
    }
}