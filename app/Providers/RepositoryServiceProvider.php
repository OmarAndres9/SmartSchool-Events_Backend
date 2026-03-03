<?php

namespace App\Providers;

//macheo de Recuros
use App\Repository\Interfaces\RecursosInterfaces;
use App\Repository\Eloquent\RecursosRepository;

//macheo de usarios

use App\Repository\Interfaces\UsuariosInterfaces;
use App\Repository\Eloquent\UsuariosRepository;


//macheo de eventos
use App\Repository\Interfaces\EventosInterfaces;
use App\Repository\Eloquent\EventosRepository;
//

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    
    public function register(): void
    {
        $this->app->bind(RecursosInterfaces::class, RecursosRepository::class);
        $this->app->bind(UsuariosInterfaces::class, UsuariosRepository::class);
        $this->app->bind(EventosInterfaces::class, EventosRepository::class);
    
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        
    }
}
