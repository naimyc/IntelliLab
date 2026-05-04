<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('app');
    });

//Fixes any Laravel fallback route
// Route::view('/{any}', 'app')->where('any', '.*');

