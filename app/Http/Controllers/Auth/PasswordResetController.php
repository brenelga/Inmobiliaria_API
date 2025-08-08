<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    
    public function sendResetCode(Request $request)
    {
        $request->validate(['email' => 'required|email|max:255']);
        
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No existe ningún usuario con este correo electrónico'
            ], 404);
        }
        
        
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        
        PasswordResetToken::updateOrCreate(
            ['email' => $user->email],
            [
                'token' => $code,
                'created_at' => Carbon::now(),
                'user_id' => $user->id
            ]
        );
        
        try {
            
            Mail::to($user->email)->send(new PasswordResetMail($code, $user->username));
            
            return response()->json([
                'success' => true,
                'message' => 'Código de verificación enviado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al enviar el correo electrónico',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
    public function validateResetCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'code' => 'required|string|size:6'
        ]);
        
        $token = PasswordResetToken::where('email', $request->email)
            ->where('token', $request->code)
            ->first();
        
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Código inválido o expirado'
            ], 400);
        }
        
        
        if (Carbon::parse($token->created_at)->addMinutes(15)->isPast()) {
            $token->delete();
            return response()->json([
                'success' => false,
                'message' => 'El código ha expirado'
            ], 400);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Código válido',
            'user_id' => $token->user_id
        ]);
    }

    
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'code' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed'
        ]);
        
        
        $token = PasswordResetToken::where('email', $request->email)
            ->where('token', $request->code)
            ->first();
        
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Código inválido o expirado'
            ], 400);
        }
        
        
        if (Carbon::parse($token->created_at)->addMinutes(15)->isPast()) {
            $token->delete();
            return response()->json([
                'success' => false,
                'message' => 'El código ha expirado'
            ], 400);
        }
        
        
        $user = User::find($token->user_id);
        $user->password = ($request->password);
        $user->save();
        
        PasswordResetToken::where('email', $request->email)->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada correctamente'
        ]);
    }
}