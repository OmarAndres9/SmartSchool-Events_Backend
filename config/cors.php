<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CORS — rutas cubiertas
    |--------------------------------------------------------------------------
    | api/* cubre /api/v1/login, /api/v1/eventos, etc.
    */
    'paths' => ['*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

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
