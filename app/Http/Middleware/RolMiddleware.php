<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class RolMiddleware
{
    /**
     * OPTIMIZACIÓN: cachear los roles del usuario en Redis (TTL 5 min).
     * Antes: cada request protegido lanzaba un SELECT + JOIN con tablas de Spatie.
     * Ahora: la primera vez se cachea; los siguientes requests no tocan la BD.
     *
     * Cache se invalida automáticamente en logout y en cambio de roles
     * (ver UsuariosRepository::update y UsuariosController::assignRoles).
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

        // Cachear nombres de roles por usuario — TTL 5 minutos (300 s)
        $cacheKey   = "user_roles_{$user->id}";
        $userRoles  = Cache::remember($cacheKey, 300, function () use ($user) {
            return $user->getRoleNames()->toArray(); // Spatie: array de strings
        });

        $tieneRol = count(array_intersect($roles, $userRoles)) > 0;

        if (! $tieneRol) {
            return response()->json(['error' => 'Forbidden: Roles Inválidos'], 403);
        }

        return $next($request);
    }
}
