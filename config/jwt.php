<?php

/*
 * config/jwt.php
 * Configuración de tymon/jwt-auth para SmartSchool.
 * Generá tu clave con: php artisan jwt:secret
 */

return [

    /*
    |--------------------------------------------------------------------------
    | JWT Authentication Secret
    |--------------------------------------------------------------------------
    | Clave secreta para firmar los tokens. Se carga desde JWT_SECRET en .env.
    | Generala con: php artisan jwt:secret
    */
    'secret' => env('JWT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | JWT Authentication Keys (RS256 — opcional)
    |--------------------------------------------------------------------------
    | Solo necesario si usás algoritmo RS256 con claves asimétricas.
    */
    'keys' => [
        'public'  => env('JWT_PUBLIC_KEY'),
        'private' => env('JWT_PRIVATE_KEY'),
        'passphrase' => env('JWT_PASSPHRASE'),
    ],

    /*
    |--------------------------------------------------------------------------
    | JWT time to live
    |--------------------------------------------------------------------------
    | Tiempo de vida del token en minutos. null = sin expiración.
    */
    'ttl' => env('JWT_TTL', 1440), // 24 horas

    /*
    |--------------------------------------------------------------------------
    | Refresh time to live
    |--------------------------------------------------------------------------
    | Tiempo en minutos para refrescar el token (desde su creación).
    */
    'refresh_ttl' => env('JWT_REFRESH_TTL', 20160), // 2 semanas

    /*
    |--------------------------------------------------------------------------
    | JWT hashing algorithm
    |--------------------------------------------------------------------------
    | Algoritmo de firma. HS256 es el más común para clave simétrica.
    | Opciones: HS256, HS384, HS512, RS256, RS384, RS512, ES256, ES384, ES512
    */
    'algo' => env('JWT_ALGO', Tymon\JWTAuth\Providers\JWT\Provider::ALGO_HS256),

    /*
    |--------------------------------------------------------------------------
    | Required Claims
    |--------------------------------------------------------------------------
    | Claims obligatorios que deben estar presentes en el payload.
    */
    'required_claims' => [
        'iss', 'iat', 'exp', 'nbf', 'sub', 'jti',
    ],

    /*
    |--------------------------------------------------------------------------
    | Persistent Claims
    |--------------------------------------------------------------------------
    | Claims que se conservan al refrescar el token.
    */
    'persistent_claims' => [],

    /*
    |--------------------------------------------------------------------------
    | Lock Subject
    |--------------------------------------------------------------------------
    | Si es true, el subject se bloquea al primer uso del token.
    */
    'lock_subject' => true,

    /*
    |--------------------------------------------------------------------------
    | Leeway
    |--------------------------------------------------------------------------
    | Segundos de tolerancia para validar nbf/exp (útil con relojes desfasados).
    */
    'leeway' => env('JWT_LEEWAY', 0),

    /*
    |--------------------------------------------------------------------------
    | Blacklist Enabled
    |--------------------------------------------------------------------------
    | Si es true, los tokens invalidados (logout) se guardan en la blacklist.
    */
    'blacklist_enabled' => env('JWT_BLACKLIST_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Blacklist Grace Period
    |--------------------------------------------------------------------------
    | Segundos de gracia para tokens en la blacklist (evita problemas con
    | múltiples peticiones simultáneas al hacer logout).
    */
    'blacklist_grace_period' => env('JWT_BLACKLIST_GRACE_PERIOD', 0),

    /*
    |--------------------------------------------------------------------------
    | Decrypt Cookies
    |--------------------------------------------------------------------------
    */
    'decrypt_cookies' => false,

    /*
    |--------------------------------------------------------------------------
    | Providers
    |--------------------------------------------------------------------------
    */
    'providers' => [
        'jwt'     => Tymon\JWTAuth\Providers\JWT\Lcobucci::class,
        'auth'    => Tymon\JWTAuth\Providers\Auth\Illuminate::class,
        'storage' => Tymon\JWTAuth\Providers\Storage\Illuminate::class,
    ],

];
