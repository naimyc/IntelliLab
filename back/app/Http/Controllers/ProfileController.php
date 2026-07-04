<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller {

    // PATCH /api/profile — Name und Rolle bearbeiten
    public function update(Request $request): JsonResponse {
        $data = $request->validate([
            'name'  => 'sometimes|string|max:255',
            'rolle' => 'sometimes|string|in:Student,Studentin,Tutor,Tutorin,Dozent,Dozentin,Professor,Professorin',
        ]);
        $request->user()->update($data);
        return response()->json(['user' => $request->user()->fresh()]);
    }

    // POST /api/profile/avatar — Profilbild hochladen
    public function uploadAvatar(Request $request): JsonResponse {
        $request->validate(['avatar' => 'required|image|max:2048']);
        $path = $request->file('avatar')->store('avatars', 'public');
        $url  = Storage::url($path);
        $request->user()->update(['avatar_url' => $url]);
        return response()->json(['avatar_url' => $url]);
    }

    // GET /api/analyse-ergebnis?language=Java
    public function analyseErgebnis(Request $request): JsonResponse {
        $language = $request->query('language');
        $user     = $request->user();

        // Sprachen bei denen der User abgeschlossene Projekte hat
        $doneLanguages = $user->projects()
            ->where('abgeschlossen', true)
            ->pluck('language')
            ->unique()
            ->values();

        if (!$language) {
            return response()->json(['languages' => $doneLanguages]);
        }

        // Prüfen ob Projekt in dieser Sprache existiert und abgeschlossen
        $hasProject = $user->projects()
            ->where('language', $language)
            ->where('abgeschlossen', true)
            ->exists();

        if (!$hasProject) {
            $hasAny = $user->projects()->where('language', $language)->exists();
            return response()->json([
                'error'   => true,
                'has_any' => $hasAny,
                'message' => $hasAny
                    ? "Du hast ein Projekt in $language, aber noch nicht abgeschlossen."
                    : "Du hast noch kein Projekt in $language. Erstelle und schließe eines ab.",
            ], 200);
        }

        $ergebnis = $user->analyseErgebnis()->where('language', $language)->first();
        return response()->json(['ergebnis' => $ergebnis, 'language' => $language]);
    }
}
