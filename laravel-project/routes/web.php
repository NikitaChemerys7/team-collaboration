<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/sanctum/csrf-cookie', function () {
    return response()->json(['message' => 'CSRF cookie set']);
});

Route::get('/reset-password/{token}', function ($token) {
    return response()->json([
        'token' => $token,
        'email' => request()->query('email')
    ]);
})->name('password.reset');

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::findOrFail($id);

    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        abort(403);
    }

    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
    }

    $token = $user->createToken('API Token')->plainTextToken;

    return redirect()->away("http://localhost:5173/email-verification-waiting?verified=1&email={$user->email}&token={$token}");
})->middleware(['signed'])->name('verification.verify');
