<?php

return [

    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        // ── Desarrollo local ──────────────────────────────────────────────
        'http://localhost:5173',
        'http://localhost:5174',
        'http://127.0.0.1:5173',
        'http://127.0.0.1:5174',
        'http://localhost:3000',
        // ── GitHub Codespaces / GitHub Dev ────────────────────────────────
        // Se usa patrón en allowed_origins_patterns (abajo) para cubrir
        // cualquier subdominio dinámico de Codespaces y GitHub.dev
    ],

    'allowed_origins_patterns' => [
        // Cubre CUALQUIER URL de GitHub Codespaces (puerto dinámico incluido)
        '#^https://.*\.app\.github\.dev$#',
        // Cubre GitHub.dev editor
        '#^https://.*\.github\.dev$#',
        // Cubre GitHub Codespaces con subdominio distinto
        '#^https://.*\.preview\.app\.github\.dev$#',
    ],

    'allowed_headers' => [
        'Content-Type',
        'Accept',
        'Authorization',
        'X-Requested-With',
        'Origin',
    ],

    'exposed_headers' => [],

    'max_age' => 86400,

    'supports_credentials' => true,

];
