<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Tymon\JWTAuth\Providers\LaravelServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar JWT Service Provider manualmente
        // (necesario en Laravel 11 donde no existe config/app.php providers[])
        $this->app->register(LaravelServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Forzar que Spatie use el guard 'api' por defecto
        // Evita el error "There is no guard named api" al arrancar
        app(\Spatie\Permission\PermissionRegistrar::class)
            ->setPermissionClass(\Spatie\Permission\Models\Permission::class);
    }
}
