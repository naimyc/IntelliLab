<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\GptController;

/*
|--------------------------------------------------------------------------
| IntelliLab Auth Routes
|--------------------------------------------------------------------------
|
| All routes are prefixed with /api (set in bootstrap/app.php or RouteServiceProvider).
|
| Public routes — no session required
| Protected routes — require valid Sanctum session cookie
|
*/

// ── Public ────────────────────────────────────────────────────────────────

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// ── Protected (Sanctum session auth) ─────────────────────────────────────
Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);
});

Route::middleware('auth:sanctum')->group(function () {
    // Plain chat (existing ChatView)
    Route::post('/chat', [ChatController::class, 'chat']);

    // Exercise generation — UploadView -> EditorView flow.
    // System prompt is fixed server-side; client only sends { topic }.
    Route::post('/generate', [ChatController::class, 'generate']);

    // Generic GPT endpoint for app-wide use (prompt | system+prompt | messages).
    Route::post('/gpt', [GptController::class, 'generate']);
});