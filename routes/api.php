<?php

use App\Http\Controllers\Api\v1\UserController;
use App\Http\Controllers\Api\v1\UserEmailVerificationController;
use App\Http\Controllers\Api\v1\UserTokenController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware(['auth:sanctum', 'verified']);

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::delete('users/auth/token', [UserTokenController::class, 'destroy']);
});

Route::post('users', [UserController::class, 'store']);

Route::post('users/auth/token', [UserTokenController::class, 'store']);

Route::get('/', function (Request $request) {
    return response()->json([
        'message' => 'Welcome to the Dear Future API!',
    ]);
});

Route::get('/email/verify/{id}/{hash}', UserEmailVerificationController::class)->middleware('signed')->name('verification.verify');
