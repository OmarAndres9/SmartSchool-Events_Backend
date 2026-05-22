<?php

use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventosController;
use App\Http\Controllers\NotificacionesController;
use App\Http\Controllers\RecursosController;
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
    Route::get('eventos',                  [EventosController::class, 'index']);
    Route::get('eventos/mis-eventos',      [EventosController::class, 'misEventos']);
    Route::get('eventos/tipo/{tipo}',      [EventosController::class, 'getEventosByTipo']);
    Route::get('eventos/{id}',             [EventosController::class, 'show']);

    // Dashboard
    Route::get('dashboard/stats', [DashboardController::class, 'stats']);

    // Inscripciones a eventos
    Route::post('eventos/{id}/inscribir',    [EventosController::class, 'inscribir']);
    Route::delete('eventos/{id}/desinscribir', [EventosController::class, 'desinscribir']);
    Route::get('eventos/{id}/inscritos',     [EventosController::class, 'inscritos']);
    Route::get('mis-inscripciones',          [EventosController::class, 'misInscripciones']);

    // Notificaciones — cualquier usuario autenticado
    Route::apiResource('notificaciones', NotificacionesController::class);

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
        Route::apiResource('recursos', RecursosController::class);
        Route::apiResource('reportes', ReportesController::class);

        Route::post('eventos',                  [EventosController::class, 'store']);
        Route::put('eventos/{id}',               [EventosController::class, 'update']);
        Route::delete('eventos/{id}',            [EventosController::class, 'destroy']);
        Route::post('eventos/{id}/recursos',      [EventosController::class, 'asignarRecurso']);
        Route::delete('eventos/{id}/recursos/{recurso}', [EventosController::class, 'desasignarRecurso']);
    });
});
