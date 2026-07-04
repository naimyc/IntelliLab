<?php
namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\AnalyseErgebnis;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller {

    // GET /api/projects — alle Projekte des Users
    public function index(Request $request): JsonResponse {
        $projects = $request->user()->projects()->orderByDesc('updated_at')->get();
        return response()->json(['projects' => $projects]);
    }

    // POST /api/projects — neues Projekt anlegen (aus UploadView)
    public function store(Request $request): JsonResponse {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'language'      => 'required|string|max:100',
            'topic'         => 'nullable|string|max:255',
            'total_tasks'   => 'integer|min:1',
            'exercise_data' => 'nullable|array',
        ]);
        $project = $request->user()->projects()->create($data);
        return response()->json(['project' => $project], 201);
    }

    // PATCH /api/projects/{id} — Fortschritt / Abschluss aktualisieren
    public function update(Request $request, int $id): JsonResponse {
        $project = Project::where('id', $id)->where('user_id', $request->user()->id)->firstOrFail();
        $data = $request->validate([
            'completed_tasks' => 'integer|min:0',
            'abgeschlossen'   => 'boolean',
            'ki_feedback'     => 'nullable|string',
            'exercise_data'   => 'nullable|array',
        ]);
        $project->update($data);
        return response()->json(['project' => $project]);
    }

    // DELETE /api/projects/{id}
    public function destroy(Request $request, int $id): JsonResponse {
        Project::where('id', $id)->where('user_id', $request->user()->id)->firstOrFail()->delete();
        return response()->json(['message' => 'Projekt gelöscht.']);
    }

    // GET /api/projects/stats — Zahlen für Dashboard
    public function stats(Request $request): JsonResponse {
        $user = $request->user();
        $total     = $user->projects()->count();
        $done      = $user->projects()->where('abgeschlossen', true)->count();
        $recent    = $user->projects()->orderByDesc('updated_at')->limit(3)->get();
        return response()->json([
            'total'   => $total,
            'done'    => $done,
            'recent'  => $recent,
        ]);
    }

    // POST /api/projects/{id}/analyse — KI-Analyse aktualisieren (nach Prüfen)
    public function updateAnalyse(Request $request, int $id): JsonResponse {
        $project = Project::where('id', $id)->where('user_id', $request->user()->id)->firstOrFail();
        $feedback = $request->input('ki_feedback', '');
        $project->update(['ki_feedback' => $feedback]);

        // Analyse-Ergebnis für diese Sprache aktualisieren / erstellen
        $language = $project->language;
        $gelernt     = $request->input('gelernt', '');
        $verbessern  = $request->input('verbessern', '');
        $nochLernen  = $request->input('noch_lernen', '');

        AnalyseErgebnis::updateOrCreate(
            ['user_id' => $request->user()->id, 'language' => $language],
            ['gelernt' => $gelernt, 'verbessern' => $verbessern, 'noch_lernen' => $nochLernen]
        );

        return response()->json(['message' => 'Analyse aktualisiert.']);
    }
}
