<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class RolMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  mixed  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Verifica si el usuario tiene al menos uno de los roles pasados (Spatie hasAnyRole)
        if (! $user->hasAnyRole($roles)) {
            return response()->json(['error' => 'Forbidden: Roles Inválidos'], 403);
        }

        return $next($request);
    }
}
