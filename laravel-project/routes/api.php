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
use App\Http\Controllers\DocumentController;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

// Public routes
Route::get('/conferences', [ConferenceController::class, 'index']);
Route::get('/conferences/{conference}', [ConferenceController::class, 'show']);
Route::get('/conferences/{conference}/subpages', [SubpageController::class, 'index']);
Route::get('/conferences/{conference}/subpages/{subpage}', [SubpageController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/user/managed-years', [AuthController::class, 'getManagedYears']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::put('/profile', [ProfileController::class, 'update']);

    // Conference routes
    Route::get('/conferences/editable', [ConferenceController::class, 'getEditableConferences']);
    Route::post('/conferences', [ConferenceController::class, 'store']);
    Route::put('/conferences/{conference}', [ConferenceController::class, 'update'])->middleware('can.manage.conference');
    Route::delete('/conferences/{conference}', [ConferenceController::class, 'destroy'])->middleware('can.manage.conference');

    // Subpage routes
    Route::middleware(['can.manage.subpage'])->group(function () {
        Route::post('/conferences/{conference}/subpages', [SubpageController::class, 'store']);
        Route::put('/conferences/{conference}/subpages/{subpage}', [SubpageController::class, 'update']);
        Route::delete('/conferences/{conference}/subpages/{subpage}', [SubpageController::class, 'destroy']);
    });

    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [AuthController::class, 'getUsers']);
        Route::post('/users', [AuthController::class, 'createUser']);
        Route::put('/users/{id}', [AuthController::class, 'updateUser']);
        Route::delete('/users/{id}', [AuthController::class, 'deleteUser']);
        Route::get('/years/{year}/editors', [ConferenceController::class, 'getEditors']);
        Route::post('/years/{year}/assign-editor', [ConferenceController::class, 'assignEditor']);
        Route::post('/years/{year}/remove-editor', [ConferenceController::class, 'removeEditor']);
        Route::post('/conferences/{conference}/hero-image', [ConferenceController::class, 'uploadHeroImage']);
        Route::delete('/conferences/{conference}/hero-image', [ConferenceController::class, 'removeHeroImage']);
    });

    Route::get('/documents', [DocumentController::class, 'index']);
    Route::post('/documents', [DocumentController::class, 'store']);
    Route::delete('/documents/{id}', [DocumentController::class, 'destroy']);
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
