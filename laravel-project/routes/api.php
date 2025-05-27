<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Models\User;
use App\Http\Controllers\MailController;

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);


Route::middleware('auth:sanctum')->group(function () {
     Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::middleware('role:admin')->group(function () {
        
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