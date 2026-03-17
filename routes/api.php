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

        // FIX: ruta para asignar un recurso a un evento
        // Usada por DetalleRecurso.jsx → POST /api/v1/eventos/:id/recursos
        Route::post('eventos/{evento}/recursos', function (Request $request, \App\Models\Eventos $evento) {
            $request->validate([
                'id_recurso' => 'required|exists:_recursos__table,id',
                'cantidad'   => 'nullable|integer|min:1',
            ]);

            $evento->recursos()->syncWithoutDetaching([
                $request->id_recurso => ['cantidad' => $request->cantidad ?? 1],
            ]);

            return response()->json([
                'message'  => 'Recurso asignado al evento correctamente',
                'evento'   => $evento->load('recursos'),
            ]);
        });
    });
});
