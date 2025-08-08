<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Jenssegers\Mongodb\Eloquent\Model;

class MongoSanctumAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        if ($token = $request->bearerToken()) {
            $hashedToken = hash('sha256', $token);
            
            $accessToken = PersonalAccessToken::where('token', $hashedToken)->first();
            
            if ($accessToken) {
                // Obtiene el modelo de usuario desde MongoDB
                $user = $accessToken->tokenable;
                
                if ($user) {
                    auth()->login($user);
                    return $next($request);
                }
            }
        }

        return response()->json(['message' => 'Unauthenticated'], 401);
    }
}