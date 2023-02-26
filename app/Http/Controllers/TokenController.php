<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TokenRequest;
use Illuminate\Http\JsonResponse;

class TokenController extends Controller
{
    const TOKEN_NAME = 'api';

    /**
     * Fetch a token 
     */
    public function fetch(TokenRequest $request) 
    {
        $request->authenticate();

        $plainToken = $request->user()
            ->createToken(self::TOKEN_NAME)
            ->plainTextToken;

        return response(['token' => $plainToken]);
    }

    /**
     *  Revoke the token that was used to authenticate the current request
     */
    public function revoke(Request $request) : JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json();
    }
}
