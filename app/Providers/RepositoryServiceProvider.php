<?php

namespace App\Providers;

use App\Repository\Eloquent\AuthRepository;
use App\Repository\Eloquent\EventosRepository;
use App\Repository\Eloquent\NotificacionesRepository;
use App\Repository\Eloquent\RecursosRepository;
use App\Repository\Eloquent\ReporteRepository;
use App\Repository\Eloquent\RolesRepository;
use App\Repository\Eloquent\UsuariosRepository;
use App\Repository\Interfaces\AuthInterfaces;
use App\Repository\Interfaces\EventosInterfaces;
use App\Repository\Interfaces\NotificacionesInterfaces;
use App\Repository\Interfaces\RecursosInterfaces;
use App\Repository\Interfaces\ReporteInterfaces;
use App\Repository\Interfaces\RolesInterfaces;
use App\Repository\Interfaces\UsuariosInterfaces;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // FIX: faltaba el binding de AuthInterfaces → AuthRepository
        // Sin esto, AutService no puede resolver su dependencia y la app falla al arrancar
        $this->app->bind(AuthInterfaces::class,         AuthRepository::class);

        $this->app->bind(RecursosInterfaces::class,     RecursosRepository::class);
        $this->app->bind(UsuariosInterfaces::class,     UsuariosRepository::class);
        $this->app->bind(EventosInterfaces::class,      EventosRepository::class);
        $this->app->bind(ReporteInterfaces::class,      ReporteRepository::class);
        $this->app->bind(NotificacionesInterfaces::class, NotificacionesRepository::class);
        $this->app->bind(RolesInterfaces::class,        RolesRepository::class);
    }

    public function boot(): void {}
}
