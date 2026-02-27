<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class UserEmailVerificationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, $id, $hash)
    {
        if (! URL::hasValidSignature($request)) {
            return response()->json(['message' => 'Link inválido ou expirado'], 400);
        }

        $user = User::find($id);

        if (!$user || !hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return response()->json(['message' => 'Invalid verification link'], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 400);
        }

        $user->markEmailAsVerified();

        return response()->json(['message' => 'Email verified successfully']);
    }
}
