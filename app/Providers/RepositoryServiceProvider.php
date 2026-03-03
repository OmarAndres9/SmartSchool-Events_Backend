<?php

namespace App\Providers;
use App\Repository\Interfaces\RecursosInterfaces;
use App\Repository\Eloquent\RecursosRepository;


use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    
    public function register(): void
    {
        $this->app->bind(RecursosInterfaces::class, RecursosRepository::class);
        
        }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        
    }
}
