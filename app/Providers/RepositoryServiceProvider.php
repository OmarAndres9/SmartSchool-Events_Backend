<?php

namespace App\Providers;

//macheo de Recuros
use App\Repository\Interfaces\RecursosInterfaces;
use App\Repository\Eloquent\RecursosRepository;

//macheo de usarios

use App\Repository\Interfaces\UsuariosInterfaces;
use App\Repository\Eloquent\UsuariosRepository;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    
    public function register(): void
    {
        $this->app->bind(RecursosInterfaces::class, RecursosRepository::class);
        $this->app->bind(UsuariosInterfaces::class, UsuariosRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        
    }
}
