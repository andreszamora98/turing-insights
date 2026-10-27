<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ResourceController;
use App\Http\Controllers\Api\TopicController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::get('/resources', [ResourceController::class, 'index']);
Route::get('/resources/{slug}', [ResourceController::class, 'show']);
Route::get('/topics', [TopicController::class, 'index']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::middleware('admin')->group(function () {
        Route::post('/resources', [ResourceController::class, 'store']);
        Route::put('/resources/{resource}', [ResourceController::class, 'update']);
        Route::delete('/resources/{resource}', [ResourceController::class, 'destroy']);

        Route::post('/topics', [TopicController::class, 'store']);
        Route::put('/topics/{topic}', [TopicController::class, 'update']);
        Route::delete('/topics/{topic}', [TopicController::class, 'destroy']);
    });
});