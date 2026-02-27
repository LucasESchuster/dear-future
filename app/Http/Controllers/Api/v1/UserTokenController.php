<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\StoreUserTokenRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserTokenController extends Controller
{

    public function store(StoreUserTokenRequest $request)
    {

        $validated = $request->validated();

        $user = User::where('email', $validated['email'])->first();

        if (!$user->hasVerifiedEmail()) {
            return response()->json(['message' => 'User is not verified, please check the email verification link'], 400);
        }

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json(['message' => 'Wrong credentials'], 401);
        }

        $user->tokens()->delete();

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function destroy()
    {
        request()->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}
