<?php

namespace App\Providers;
use Illuminate\Support\ServiceProvider;
//macheo de recursos
use App\Repository\Interfaces\RecursosInterfaces;
use App\Repository\Eloquent\RecursosRepository;

//macheo de usuarios
use App\Repository\Interfaces\UsuariosInterfaces;
use App\Repository\Eloquent\UsuariosRepository;

//macheo de eventos
use App\Repository\Interfaces\EventosInterfaces;
use App\Repository\Eloquent\EventosRepository;

//macheo de reportes
use App\Repository\Interfaces\ReporteInterfaces;
use App\Repository\Eloquent\ReporteRepository;

//macheo de notificaciones
use App\Repository\Interfaces\NotificacionesInterfaces;
use App\Repository\Eloquent\NotificacionesRepository;

//macheo de recuperacion


class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(RecursosInterfaces::class, RecursosRepository::class);
        $this->app->bind(UsuariosInterfaces::class, UsuariosRepository::class);
        $this->app->bind(EventosInterfaces::class, EventosRepository::class);
        $this->app->bind(ReporteInterfaces::class, ReporteRepository::class);
        $this->app->bind(NotificacionesInterfaces::class, NotificacionesRepository::class);
        $this->app->bind(MailService::class, fn() => new MailService());
        $this->app->bind(PasswordResetService::class, fn($app) => new PasswordResetService(
            $app->make(MailService::class)
        ));
    }

    public function boot(): void
    {
    }
}