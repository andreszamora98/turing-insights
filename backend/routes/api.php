<?php

use App\Http\Controllers\Api\ResourceController;
use Illuminate\Support\Facades\Route;

Route::get('/resources', [ResourceController::class, 'index']);
Route::get('/resources/{slug}', [ResourceController::class, 'show']);