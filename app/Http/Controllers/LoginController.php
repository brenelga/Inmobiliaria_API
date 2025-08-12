<?php

namespace App\Http\Controllers;

use App\Models\UsuarioModel as User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string'
        ]);
        
        $user = User::where('nombre_usuario.usuario', $request->username)->first();
        
        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $vigenereResponse = Http::post('http://localhost:8800/vigenere/cifrar', [
            'clave' => $request->username,
            'texto' => $request->password
        ]);

        

        if ($vigenereResponse->failed()) {
            return response()->json(['message' => 'Error cifrando la contraseña'], 500);
        }

        $responseData = $vigenereResponse->json();
        $encryptedPassword = $responseData['resultado'];

        $hashedPassword = $user['contrasena'];

        if (!Hash::check($encryptedPassword, $hashedPassword)) {
            return response()->json(['message' => 'Contraseña incorrecta'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => (string) $user->_id,
                'username' => $user->nombre_usuario['usuario'],
                'tipo' => $user->nombre_usuario['tipo']
            ]
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
