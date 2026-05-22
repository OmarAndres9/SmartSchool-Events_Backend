<?php

use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EstudianteDashboardController;
use App\Http\Controllers\EventosController;
use App\Http\Controllers\NotificacionesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecursosController;
use App\Http\Controllers\RepresentanteDashboardController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\UsuariosController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — v1
|--------------------------------------------------------------------------
| Prefijo: /api/v1  (configurado en bootstrap/app.php)
*/

// ── Rutas Públicas ────────────────────────────────────────────────────────────
Route::post('login',          [AuthController::class, 'login'])->middleware('throttle:5,1');
Route::post('register',       [AuthController::class, 'register']);
Route::post('password/email', [\App\Http\Controllers\Api\PasswordResetController::class, 'sendResetLinkEmail'])->middleware('throttle:3,5');
Route::post('password/reset', [\App\Http\Controllers\Api\PasswordResetController::class, 'reset']);

// ── Rutas Protegidas ──────────────────────────────────────────────────────────
Route::middleware('auth:api')->group(function () {

    // Sesión
    Route::post('logout',  [AuthController::class, 'logout']);
    Route::get('me',       [AuthController::class, 'me']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    // Eventos — lectura (todos los autenticados)
    Route::get('eventos',                   [EventosController::class, 'index']);
    Route::get('eventos/mis-eventos',       [EventosController::class, 'misEventos']);
    Route::get('eventos/tipo/{tipo}',       [EventosController::class, 'getEventosByTipo']);
    Route::get('eventos/historial',         [EventosController::class, 'historial']);
    Route::get('eventos/proximos',          [EventosController::class, 'proximos']);
    Route::get('eventos/favoritos',         [EventosController::class, 'favoritos']);
    Route::get('eventos/{id}/instancias',   [EventosController::class, 'instancias']);
    Route::get('eventos/{id}',              [EventosController::class, 'show']);

    // Perfil
    Route::get('me/perfil',       [ProfileController::class, 'perfil']);
    Route::put('me/perfil',       [ProfileController::class, 'actualizarPerfil']);

    // Dashboard
    Route::get('dashboard/stats', [DashboardController::class, 'stats']);

    // Estudiante Dashboard
    Route::get('estudiante/notas',        [EstudianteDashboardController::class, 'notas']);
    Route::get('estudiante/promedios',    [EstudianteDashboardController::class, 'promedios']);
    Route::get('estudiante/eventos',      [EstudianteDashboardController::class, 'eventos']);
    Route::get('estudiante/periodos',     [EstudianteDashboardController::class, 'periodos']);

    // Representante Dashboard
    Route::get('representante/estudiantes',                            [RepresentanteDashboardController::class, 'estudiantes']);
    Route::get('representante/estudiantes/{id}/notas',                 [RepresentanteDashboardController::class, 'notasEstudiante']);
    Route::get('representante/estudiantes/{id}/promedios',             [RepresentanteDashboardController::class, 'promediosEstudiante']);
    Route::get('representante/estudiantes/{id}/eventos',               [RepresentanteDashboardController::class, 'eventosEstudiante']);

    // Citas
    Route::get('citas',                     [CitaController::class, 'index']);
    Route::post('citas',                    [CitaController::class, 'store']);
    Route::get('citas/pendientes',          [CitaController::class, 'pendientes']);
    Route::patch('citas/{id}/aprobar',      [CitaController::class, 'aprobar']);
    Route::patch('citas/{id}/rechazar',     [CitaController::class, 'rechazar']);

    // Inscripciones a eventos
    Route::post('eventos/{id}/inscribir',    [EventosController::class, 'inscribir']);
    Route::delete('eventos/{id}/desinscribir', [EventosController::class, 'desinscribir']);
    Route::get('eventos/{id}/inscritos',     [EventosController::class, 'inscritos']);
    Route::get('mis-inscripciones',          [EventosController::class, 'misInscripciones']);

    // Favoritos (mutaciones)
    Route::post('eventos/{id}/favorito',     [EventosController::class, 'marcarFavorito']);
    Route::delete('eventos/{id}/favorito',   [EventosController::class, 'desmarcarFavorito']);

    // Valoraciones
    Route::post('eventos/{id}/valorar',      [EventosController::class, 'valorar']);
    Route::get('eventos/{id}/valoraciones',  [EventosController::class, 'valoraciones']);

    // Calendario
    Route::get('calendario',                 [EventosController::class, 'calendario']);

    // Notificaciones — cualquier usuario autenticado
    Route::apiResource('notificaciones', NotificacionesController::class);

    // Recursos — lectura pública (autenticados)
    Route::get('recursos',        [RecursosController::class, 'index']);
    Route::get('recursos/{id}',   [RecursosController::class, 'show']);

    // Docente — gestión de notas
    Route::middleware('role:admin,organizador,docente')->group(function () {
        Route::get('docente/materias',    [\App\Http\Controllers\DocenteController::class, 'materias']);
        Route::get('docente/estudiantes', [\App\Http\Controllers\DocenteController::class, 'estudiantes']);
        Route::post('docente/notas',      [\App\Http\Controllers\DocenteController::class, 'guardarNota']);
    });

    // Solo Administradores
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('roles',       RoleController::class);
        Route::apiResource('permissions', PermissionController::class);
        Route::apiResource('usuarios',    UsuariosController::class);

        // Asignar/cambiar roles a un usuario (Spatie syncRoles)
        Route::post('users/{user}/roles', function (Request $request, \App\Models\User $user) {
            $request->validate(['roles' => 'required|array']);
            $user->syncRoles($request->roles);
            return response()->json([
                'message' => 'Roles asignados correctamente',
                'user'    => $user->load('roles'),
            ]);
        });
    });

    // Administradores y Organizadores
    Route::middleware('role:admin,organizador')->group(function () {
        Route::post('recursos',               [RecursosController::class, 'store']);
        Route::put('recursos/{id}',            [RecursosController::class, 'update']);
        Route::delete('recursos/{id}',         [RecursosController::class, 'destroy']);

        Route::apiResource('reportes', ReportesController::class);

        Route::post('eventos',                  [EventosController::class, 'store']);
        Route::put('eventos/{id}',               [EventosController::class, 'update']);
        Route::delete('eventos/{id}',            [EventosController::class, 'destroy']);
        Route::post('eventos/{id}/recursos',      [EventosController::class, 'asignarRecurso']);
        Route::delete('eventos/{id}/recursos/{recurso}', [EventosController::class, 'desasignarRecurso']);
        Route::post('eventos/{id}/archivos',       [EventosController::class, 'subirArchivo']);
        Route::get('eventos/{id}/archivos',        [EventosController::class, 'archivos']);
        Route::delete('eventos/{id}/archivos/{archivo}', [EventosController::class, 'eliminarArchivo']);
    });
});
