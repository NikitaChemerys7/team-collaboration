<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConferenceController;
use App\Http\Controllers\SubpageController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Models\User;
use App\Http\Controllers\MailController;

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

    // Subpage routes
    Route::get('/conferences/{conferenceId}/subpages', [SubpageController::class, 'index']);
    Route::post('/conferences/{conferenceId}/subpages', [SubpageController::class, 'store']);
    Route::get('/conferences/{conferenceId}/subpages/{subpageId}', [SubpageController::class, 'show']);
    Route::put('/conferences/{conferenceId}/subpages/{subpageId}', [SubpageController::class, 'update']);
    Route::delete('/conferences/{conferenceId}/subpages/{subpageId}', [SubpageController::class, 'destroy']);

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
Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail']);
Route::post('/password/reset', [AuthController::class, 'resetPassword']);
Route::post('/send-email', [MailController::class, 'send'])->name('send.email');

Route::get('/check-email-verified', function (Request $request) {
    $user = User::where('email', $request->query('email'))->first();

    if (!$user) {
        return response()->json(['verified' => false], 404);
    }

    if ($user->hasVerifiedEmail()) {
        $token = $user->createToken('API Token')->plainTextToken;
        return response()->json([
            'verified' => true,
            'user' => $user,
            'token' => $token
        ]);
    }

    return response()->json(['verified' => false]);
});