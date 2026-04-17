<?php

use Illuminate\Support\Facades\Route;

Route::post('/analyze-pdf', [TaskController::class, 'analyze']);
Route::post('/submit-code', [CodeController::class, 'submit']);
Route::get('/feedback/{id}', [FeedbackController::class, 'show']);

