<?php

namespace Crater\Http\Controllers\V1\Settings;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\CreateUserTokenRequest;
use Laravel\Sanctum\PersonalAccessToken;

class UserTokensController extends Controller
{
    public function listUserTokens()
    {
        $user = Auth::user();
        $formatted_tokens = $user->tokens
            ->map(function($token) {
                return $this->formatUserToken($token);
            });
        return response()->json(['success' => true, 'tokens' => $formatted_tokens]);
    }

    public function getUserToken(int $token_id)
    {
        $user = Auth::user();
        $token = $user->tokens()
          ->where('id', $token_id)
          ->firstOrFail();
        $formatted_token = $this->formatUserToken($token);
        return response()->json(['success' => true, 'token' => $formatted_token]);
    }

    public function createUserToken(CreateUserTokenRequest $request)
    {
        $user = Auth::user();
        $token = $user->createToken($request->token_name);
        return response()->json(['success' => true, 'token' => $token->plainTextToken]);
    }

    public function deleteUserToken(int $token_id)
    {
        $user = Auth::user();
        $user->tokens()->where('id', $token_id)->firstOrFail()->delete();
        return response()->json(['success' => true]);
    }

    private function formatUserToken(PersonalAccessToken $token)
    {
        return [
          'id' => $token->id,
          'name' => $token->name,
          'last_used_at' => $token->last_used_at,
          'created_at' => $token->created_at,
          'updated_at' => $token->updated_at,
        ];
    }
}
