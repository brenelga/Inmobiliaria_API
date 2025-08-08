<?php
namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return route('login');
        }
        
        return response()->json([
            'message' => 'No autenticado',
            'debug' => [
                'token_received' => $request->bearerToken(),
                'token_valid' => $request->bearerToken() ? !!\Laravel\Sanctum\PersonalAccessToken::findToken($request->bearerToken()) : false
            ]
        ], 401);
    }
}