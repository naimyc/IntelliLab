<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::put('/hello-world', function (Request $request) {
    return response()->json([
        'text' => 'Hello World!',
    ]);
});

Route::post('/analyze-pdf', [TaskController::class, 'analyze']);
Route::post('/submit-code', [CodeController::class, 'submit']);
Route::get('/feedback/{id}', [FeedbackController::class, 'show']);

