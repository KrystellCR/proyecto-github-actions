<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

// apiResource crea rutas REST: index, store, update y destroy.
Route::apiResource('tasks', TaskController::class)
    ->only(['index', 'store', 'update', 'destroy']);
