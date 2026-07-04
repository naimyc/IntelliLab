<?php

namespace App\Http\Controllers;

use App\Services\TaskGenerationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use RuntimeException;

/**
 * Verdrahtet TaskGenerationService mit dem Frontend. Das Frontend orchestriert
 * den Projekt-/Fortschritts-Zustand selbst (exercise_data via ProjectController);
 * diese Endpunkte liefern nur die eigentlichen KI-Ergebnisse.
 */
class TaskController extends Controller
{
    public function __construct(private TaskGenerationService $tasks)
    {
    }

    // POST /api/tasks/analyse — Text validieren + in Aufgaben aufteilen (Aufgabe 1 inkl. Schritte)
    public function analyse(Request $request): JsonResponse
    {
        $data = $request->validate([
            'project_name' => 'required|string|max:255',
            'language'     => 'required|string|max:100',
            'text'         => 'required|string',
        ]);

        try {
            $result = $this->tasks->analyseAndSplit($data['text'], $data['language'], $data['project_name']);
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json($result);
    }

    // POST /api/tasks/steps — Schritte für EINE Teilaufgabe on-demand generieren
    public function steps(Request $request): JsonResponse
    {
        $data = $request->validate([
            'question' => 'required|string',
            'language' => 'required|string|max:100',
        ]);

        $steps = $this->tasks->generateStepsForTask($data['question'], $data['language']);

        return response()->json(['steps' => $steps]);
    }

    // POST /api/tasks/check — eingereichten Code prüfen ("Prüfen")
    public function check(Request $request): JsonResponse
    {
        $data = $request->validate([
            'language'   => 'required|string|max:100',
            'task_title' => 'nullable|string',
            'question'   => 'nullable|string',
            'steps'      => 'nullable|array',
            'code'       => 'required|string',
        ]);

        $result = $this->tasks->pruefeCode(
            $data['language'],
            $data['task_title'] ?? '',
            $data['question'] ?? '',
            $data['steps'] ?? [],
            $data['code'],
        );

        return response()->json($result);
    }

    // POST /api/tasks/solution — Musterlösung on-demand generieren ("Lösung"-Button)
    public function solution(Request $request): JsonResponse
    {
        $data = $request->validate([
            'language'   => 'required|string|max:100',
            'task_title' => 'nullable|string',
            'question'   => 'nullable|string',
            'steps'      => 'nullable|array',
        ]);

        $code = $this->tasks->generateSolution(
            $data['language'],
            $data['task_title'] ?? '',
            $data['question'] ?? '',
            $data['steps'] ?? [],
        );

        // Modelle wrappen den Code manchmal trotz Anweisung in ```-Fences.
        $code = trim(preg_replace('/^```[a-zA-Z]*\s*|\s*```$/', '', trim($code)));

        return response()->json(['code' => $code]);
    }
}
