<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, $role)
{
    $user = $request->user();

    if (!$user) {
        return response()->json(['message' => 'No autenticado'], 401);
    }

    if ($user->tipo !== $role) {
        return response()->json(['message' => 'No autorizado'], 403);
    }

    return $next($request);
}

}
