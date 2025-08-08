<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        if (!in_array($user->nombre_usuario['tipo'] ?? null, $roles)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        return $next($request);
    }
}