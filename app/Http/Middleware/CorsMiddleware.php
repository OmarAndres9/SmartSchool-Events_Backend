<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedOrigins = [
            config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:5173')),
        ];
        $origin = $request->headers->get('origin');

        if (! in_array($origin, $allowedOrigins, true)) {
            if ($request->isMethod('OPTIONS')) {
                return response()->json(['error' => 'Forbidden origin'], 403);
            }

            return $next($request);
        }

        $headers = [
            'Access-Control-Allow-Origin'      => $origin,
            'Access-Control-Allow-Methods'     => 'GET, POST, PUT, PATCH, DELETE, OPTIONS',
            'Access-Control-Allow-Headers'     => 'Content-Type, Authorization, X-Requested-With, X-CSRF-Token, Accept',
            'Access-Control-Expose-Headers'    => 'Authorization',
            'Access-Control-Max-Age'           => '86400',
        ];

        if ($request->isMethod('OPTIONS')) {
            return response()->noContent(204)->withHeaders($headers);
        }

        $response = $next($request);
        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        return $response;
    }
}
