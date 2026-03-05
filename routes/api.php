<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\RecursosController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\EventosController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\NotificacionesController;
use App\Http\Controllers\AuthController;

// -----------------------------------------------------------------------
// Rutas Públicas
// -----------------------------------------------------------------------
Route::post('login', [AuthController::class, 'login']);

// -----------------------------------------------------------------------
// Rutas Protegidas
// -----------------------------------------------------------------------
Route::middleware('auth:api')->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    // Notificaciones
    Route::apiResource('notificaciones', NotificacionesController::class);

    // ---------------- ADMIN ----------------
    Route::middleware('role:admin')->group(function () {

        Route::apiResource('roles', RoleController::class);
        Route::apiResource('permissions', PermissionController::class);
        Route::apiResource('usuarios', UsuariosController::class);

        Route::post('users/{user}/roles', function (Request $request, \App\Models\User $user) {

            $request->validate([
                'roles' => 'required|array'
            ]);

            $user->syncRoles($request->roles);

            return response()->json([
                'message' => 'Roles asignados correctamente',
                'user' => $user->load('roles')
            ]);
        });

    });

    // ---------------- ORGANIZADOR ----------------
    Route::middleware('role:admin,organizador')->group(function () {

        Route::apiResource('eventos', EventosController::class);
        Route::apiResource('recursos', RecursosController::class);
        Route::apiResource('reportes', ReportesController::class);

    });

});

// Imports nuevos (agrégalos arriba con los demás use)
use App\Services\MailService;
use App\Services\PasswordResetService;

    

