<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CORS — rutas cubiertas
    |--------------------------------------------------------------------------
    | api/* cubre /api/v1/login, /api/v1/eventos, etc.
    */
    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
    ],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    /*
    |--------------------------------------------------------------------------
    | Orígenes permitidos
    |--------------------------------------------------------------------------
    | Incluye todas las variantes del frontend en desarrollo local.
    | En producción reemplazar por el dominio real.
    */
    'allowed_origins' => [
        // Vite dev server (puerto por defecto y alternativo)
        'http://localhost:5173',
        'http://localhost:5174',
        'http://localhost:5175',
        'http://127.0.0.1:5173',
        'http://127.0.0.1:5174',
        // Backend mismo (peticiones internas)
        'http://localhost:8080',
        'http://127.0.0.1:8080',
        // Docker interno
        'http://frontend:5173',
    ],

    /*
    |--------------------------------------------------------------------------
    | Patrones de origen (para entornos dinámicos)
    |--------------------------------------------------------------------------
    */
    'allowed_origins_patterns' => [
        // GitHub Codespaces
        '#^https://.*\.app\.github\.dev$#',
        '#^https://.*\.github\.dev$#',
        '#^https://.*\.preview\.app\.github\.dev$#',
        // Cualquier localhost con cualquier puerto (desarrollo)
        '#^http://localhost:\d+$#',
        '#^http://127\.0\.0\.1:\d+$#',
    ],

    'allowed_headers' => [
        'Content-Type',
        'Accept',
        'Authorization',
        'X-Requested-With',
        'Origin',
        'X-CSRF-TOKEN',
    ],

    'exposed_headers' => [
        'Authorization',
    ],

    'max_age' => 86400, // 24h de caché para preflight OPTIONS

    /*
    |--------------------------------------------------------------------------
    | supports_credentials
    |--------------------------------------------------------------------------
    | FIX: debe ser false cuando el frontend usa JWT en Authorization header
    | (no cookies). Con true + wildcard origin el browser bloquea la petición.
    | Solo poner true si usás Sanctum con cookies de sesión.
    */
    'supports_credentials' => false,

];
