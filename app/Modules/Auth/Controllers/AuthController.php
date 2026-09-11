<?php

declare(strict_types=1);

namespace Rominas\Auth\Controllers;

use Rominas\Auth\Requests\AuthenticateRequest;
use Rominas\Users\Model\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController
{
    public function authenticate(AuthenticateRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if (! Auth::guard('web')->once($credentials)) {
            return response()->json(null, 401);
        }

        /** @var User $user */
        $user = Auth::guard('web')->user();

        // Admin authorization runs through policies / $user->can() (Spatie's gate), never
        // through the token's abilities — nothing calls tokenCan() or Sanctum's ability
        // middleware here. So the token carries full ['*'] abilities; scoping abilities is
        // reserved for Phase 2 participant/voter tokens, where they are actually checked.
        $token = $user->createToken($credentials['email']);

        return response()->json([
            'userId' => $user->id,
            'token' => $token->plainTextToken,
        ]);
    }

    public function logout(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $user->currentAccessToken()->delete();

        return response()->json(['success' => true]);
    }
}
