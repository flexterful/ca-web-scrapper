<?php

use App\Http\Controllers\ScrapJobController;
use App\Http\Middleware\ValidateJsonMiddleware;
use Illuminate\Support\Facades\Route;

// Validate JSON request
Route::middleware(ValidateJsonMiddleware::class)->group(function () {
    Route::post('/jobs', [ScrapJobController::class, 'post']);
});

Route::delete('/jobs/{id}', [ScrapJobController::class, 'delete']);
Route::get('/jobs/{id}', [ScrapJobController::class, 'get']);
