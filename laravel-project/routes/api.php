<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConferenceController;
use App\Http\Controllers\SubpageController;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Conference routes
    Route::apiResource('conferences', ConferenceController::class);
    Route::middleware(['role:editor', 'can.manage.conference'])->group(function () {
        Route::get('/conferences/{conference}/edit', [ConferenceController::class, 'edit']);
        Route::put('/conferences/{conference}', [ConferenceController::class, 'update']);
    });

    Route::middleware('role:admin')->group(function () {
        // User management routes
        Route::get('/users', [AuthController::class, 'getUsers']);
        Route::post('/users', [AuthController::class, 'createUser']);
        Route::put('/users/{id}', [AuthController::class, 'updateUser']);
        Route::delete('/users/{id}', [AuthController::class, 'deleteUser']);
        Route::get('/conferences/{conference}/editors', [ConferenceController::class, 'getEditors']);
        Route::post('/conferences/{conference}/assign-editor', [ConferenceController::class, 'assignEditor']);
        Route::post('/conferences/{conference}/remove-editor', [ConferenceController::class, 'removeEditor']);
    });
});
