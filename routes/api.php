<?php

use App\Http\Controllers\Api\v1\UserController;
use App\Models\User;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware(['auth:sanctum', 'verified']);



Route::post('users', [UserController::class, 'store']);


Route::get('/', function (Request $request) {
    return response()->json([
        'message' => 'Welcome to the Dear Future API!',
    ]);
});

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    if (! URL::hasValidSignature($request)) {
        return response()->json(['message' => 'Link inválido ou expirado'], 400);
    }

    $user = User::findOrFail($id);

    if ($user->hasVerifiedEmail()) {
        return response()->json(['message' => 'User já possui email verificado'], 400);
    }

    if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
        return response()->json(['message' => 'Hash inválido'], 400);
    }
    
    $user->markEmailAsVerified();

    return response()->json(['message' => 'Email verificado com sucesso']);

})->middleware('signed')->name('verification.verify');
