<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
   public function changePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:8|confirmed',
    ]);

    $user = $request->user();

    if (!$user || $user->password !== $request->current_password) {
        return response()->json(['message' => 'Credenciales no válidas'], 403);
    }

    $tokensCount = $user->tokens()->count();

    $user->password = $request->new_password;
    $user->save();

    $user->tokens()->delete();

    return response()->json([
        'message' => 'Contraseña actualizada con éxito. Todas las sesiones han sido cerradas.'
    ], 200);
}
}
