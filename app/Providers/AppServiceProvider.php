<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(\Tymon\JWTAuth\Providers\LaravelServiceProvider::class);
        $this->app->register(\Spatie\Permission\PermissionServiceProvider::class);
    }

    public function boot(): void
    {
        //
    }
}
