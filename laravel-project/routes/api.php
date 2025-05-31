<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConferenceController;
use App\Http\Controllers\SubpageController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Models\User;
use App\Http\Controllers\MailController;
use App\Http\Controllers\ProfileController;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

Route::get('/conferences', [ConferenceController::class, 'index']);
Route::get('/conferences/{id}', [ConferenceController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::put('/profile', [ProfileController::class, 'update']);

    Route::post('/conferences', [ConferenceController::class, 'store']);
    Route::put('/conferences/{id}', [ConferenceController::class, 'update']);
    Route::delete('/conferences/{id}', [ConferenceController::class, 'destroy']);

    Route::middleware(['role:editor', 'can.manage.conference'])->group(function () {
        Route::get('/conferences/{conference}/edit', [ConferenceController::class, 'edit']);
        Route::put('/conferences/{conference}', [ConferenceController::class, 'update']);
    });

    Route::get('/conferences/{conferenceId}/subpages', [SubpageController::class, 'index']);
    Route::post('/conferences/{conferenceId}/subpages', [SubpageController::class, 'store']);
    Route::get('/conferences/{conferenceId}/subpages/{subpageId}', [SubpageController::class, 'show']);
    Route::put('/conferences/{conferenceId}/subpages/{subpageId}', [SubpageController::class, 'update']);
    Route::delete('/conferences/{conferenceId}/subpages/{subpageId}', [SubpageController::class, 'destroy']);

    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [AuthController::class, 'getUsers']);
        Route::post('/users', [AuthController::class, 'createUser']);
        Route::put('/users/{id}', [AuthController::class, 'updateUser']);
        Route::delete('/users/{id}', [AuthController::class, 'deleteUser']);
        Route::get('/conferences/{conference}/editors', [ConferenceController::class, 'getEditors']);
        Route::post('/conferences/{conference}/assign-editor', [ConferenceController::class, 'assignEditor']);
        Route::post('/conferences/{conference}/remove-editor', [ConferenceController::class, 'removeEditor']);
        Route::post('/conferences/{conference}/hero-image', [ConferenceController::class, 'uploadHeroImage']);
        Route::delete('/conferences/{conference}/hero-image', [ConferenceController::class, 'removeHeroImage']);
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
