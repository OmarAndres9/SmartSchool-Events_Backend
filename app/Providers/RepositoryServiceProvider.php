<?php

namespace App\Providers;

use App\Repository\Eloquent\EventosRepository;
// macheo de recursos
use App\Repository\Eloquent\NotificacionesRepository;
use App\Repository\Eloquent\RecursosRepository;
// macheo de usuarios
use App\Repository\Eloquent\ReporteRepository;
use App\Repository\Eloquent\UsuariosRepository;
// macheo de eventos
use App\Repository\Interfaces\EventosInterfaces;
use App\Repository\Interfaces\NotificacionesInterfaces;
// macheo de reportes
use App\Repository\Interfaces\RecursosInterfaces;
use App\Repository\Interfaces\ReporteInterfaces;
// macheo de notificaciones
use App\Repository\Interfaces\UsuariosInterfaces;
use Illuminate\Support\ServiceProvider;

use App\Repository\Eloquent\AuthRepository;
use App\Repository\Interfaces\AuthInterfaces;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RecursosInterfaces::class, RecursosRepository::class);
        $this->app->bind(UsuariosInterfaces::class, UsuariosRepository::class);
        $this->app->bind(EventosInterfaces::class, EventosRepository::class);
        $this->app->bind(ReporteInterfaces::class, ReporteRepository::class);
        $this->app->bind(NotificacionesInterfaces::class, NotificacionesRepository::class);
        $this->app->bind(AuthInterfaces::class, AuthRepository::class);
    }

    public function boot(): void {}
}
