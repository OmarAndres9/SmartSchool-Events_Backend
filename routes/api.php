<?php

use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\AuthController;
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
Route::post('login',          [AuthController::class, 'login']);
Route::post('register',       [AuthController::class, 'register']);
Route::post('password/email', [\App\Http\Controllers\Api\PasswordResetController::class, 'sendResetLinkEmail']);
Route::post('password/reset', [\App\Http\Controllers\Api\PasswordResetController::class, 'reset']);

// ── Rutas Protegidas ──────────────────────────────────────────────────────────
Route::middleware('auth:api')->group(function () {

    // Sesión
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me',      [AuthController::class, 'me']);

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

        // IMPORTANTE: mis-eventos ANTES del apiResource para evitar conflicto con /{id}
        Route::get('eventos/mis-eventos', [EventosController::class, 'misEventos']);

        Route::apiResource('eventos',  EventosController::class);
        Route::apiResource('recursos', RecursosController::class);
        Route::apiResource('reportes', ReportesController::class);

        // CORRECCIÓN: asignar/desasignar recurso a evento — ahora en el controlador
        // El frontend usaba POST /api/v1/eventos/:id/recursos con campo id_recurso
        // CORRECCIÓN: el campo ahora es recurso_id (consistente con la BD)
        Route::post('eventos/{id}/recursos',            [EventosController::class, 'asignarRecurso']);
        Route::delete('eventos/{id}/recursos/{recurso}', [EventosController::class, 'desasignarRecurso']);
    });
});
