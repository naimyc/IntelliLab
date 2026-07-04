<?php
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\GptController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

// ── Public ──────────────────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
});

// ── Protected ────────────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me',      [AuthController::class, 'me']);
    });

    // Chat / GPT
    Route::post('/chat',     [ChatController::class, 'chat']);
    Route::post('/generate', [ChatController::class, 'generate']);
    Route::post('/gpt',      [GptController::class,  'generate']);

    // KI-Lernworkflow (Aufgaben/Schritte/Prüfen/Lösung)
    Route::post('/tasks/analyse',  [TaskController::class, 'analyse']);
    Route::post('/tasks/steps',    [TaskController::class, 'steps']);
    Route::post('/tasks/check',    [TaskController::class, 'check']);
    Route::post('/tasks/solution', [TaskController::class, 'solution']);

    // Projekte
    Route::get('/projects',              [ProjectController::class, 'index']);
    Route::post('/projects',             [ProjectController::class, 'store']);
    Route::get('/projects/stats',        [ProjectController::class, 'stats']);
    Route::patch('/projects/{id}',       [ProjectController::class, 'update']);
    Route::delete('/projects/{id}',      [ProjectController::class, 'destroy']);
    Route::post('/projects/{id}/analyse',[ProjectController::class, 'updateAnalyse']);

    // Profil
    Route::patch('/profile',             [ProfileController::class, 'update']);
    Route::post('/profile/avatar',       [ProfileController::class, 'uploadAvatar']);
    Route::get('/analyse-ergebnis',      [ProfileController::class, 'analyseErgebnis']);
});
