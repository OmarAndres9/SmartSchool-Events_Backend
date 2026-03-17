<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // Prefijo /api/v1 para todas las rutas de la API
        api: __DIR__.'/../routes/api.php',
        apiPrefix: 'api/v1',
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        // JWT Auth — debe registrarse antes que cualquier middleware de auth
        \Tymon\JWTAuth\Providers\LaravelServiceProvider::class,
        // Spatie Permissions
        \Spatie\LaravelPermission\PermissionServiceProvider::class,
        // Repositorios + bindings de AuthInterfaces
        \App\Providers\RepositoryServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        // CORS global — debe ir antes de cualquier otro middleware
        $middleware->prepend(\Illuminate\Http\Middleware\HandleCors::class);

        $middleware->alias([
            'role' => \App\Http\Middleware\RolMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
