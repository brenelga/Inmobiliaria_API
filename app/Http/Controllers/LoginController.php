<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;


class LoginController extends Controller
{
    public function login(Request $request)
    
{
    $credentials = $request->only('username', 'password');

    $user = User::where('username', $credentials['username'])->first();

    if (!$user || $user->password !== $credentials['password']) {
        return response()->json(['message' => 'Credenciales inválidas'], 401);
    }

    $token = $user->createToken('auth_token');
    $plainTextToken = $token->plainTextToken;

    return response()->json([
    'access_token' => $plainTextToken,
    'token_type' => 'Bearer',
    'user' => [
        'id' => $user->_id,
        'username' => $user->username,
        'tipo' => $user->tipo,
    ]
], 200, [], JSON_UNESCAPED_UNICODE);

}

}

