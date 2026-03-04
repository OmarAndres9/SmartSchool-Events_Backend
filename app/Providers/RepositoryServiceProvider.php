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

//macheo de reportes
use App\Repository\Interfaces\ReporteInterfaces;
use App\Repository\Eloquent\ReporteRepository;

//

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    
    public function register(): void
    {
        $this->app->bind(RecursosInterfaces::class, RecursosRepository::class);
        $this->app->bind(UsuariosInterfaces::class, UsuariosRepository::class);
        $this->app->bind(EventosInterfaces::class, EventosRepository::class);
        $this->app->bind(ReporteInterfaces::class, ReporteRepository::class);
       

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        
    }
}
