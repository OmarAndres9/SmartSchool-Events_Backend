<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;

Route::apiResource('roles', RoleController::class);
Route::apiResource('permissions', PermissionController::class);





use App\Http\Controllers\RecursosController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\EventosController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\NotificacionesController;
use App\Http\Controllers\AuthController;

// -----------------------------------------------------------------------
// Rutas Públicas (No requieren autenticación)
// -----------------------------------------------------------------------
Route::post('login', [AuthController::class, 'login']);

// -----------------------------------------------------------------------
// Rutas Protegidas (Requieren autenticación)
// -----------------------------------------------------------------------
Route::middleware('auth:api')->group(function () {

    // Rutas accesibles por cualquier usuario autenticado
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    // Opciones para notificaciones (usualmente todos los usuarios pueden ver las suyas)
    // Se deja abierto para cualquier rol autenticado, si es necesario restringirlo se puede mover a otro grupo
    Route::apiResource('notificaciones', NotificacionesController::class);

    // -------------------------------------------------------------------
    // Rutas para Administradores
    // Tienen control total sobre roles, permisos y usuarios
    // -------------------------------------------------------------------
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('roles', RoleController::class);
        Route::apiResource('permissions', PermissionController::class);
        Route::apiResource('usuarios', UsuariosController::class);

        // Endpoint Ejemplo Asignar Roles
        Route::post('users/{user}/roles', function (Request $request, \App\Models\User $user) {
            $request->validate(['roles' => 'required|array']);
            $user->syncRoles($request->roles);
            return response()->json([
                'message' => 'Roles asignados correctamente',
                'user' => $user->load('roles')
            ]);
        });
    });

    // -------------------------------------------------------------------
    // Rutas para Organizadores (y también Administradores)
    // Pueden gestionar eventos, recursos y reportes
    // -------------------------------------------------------------------
    Route::middleware('role:admin,organizador')->group(function () {
        Route::apiResource('eventos', EventosController::class);
        Route::apiResource('recursos', RecursosController::class);
        Route::apiResource('reportes', ReportesController::class);
    });

    // -------------------------------------------------------------------
    // Rutas para Usuarios
    // Si los usuarios regulares solo deben leer eventos, se pueden separar 
    // las rutas de lectura de las de escritura aquí.
    // Por el momento, el middleware anterior controla acceso total. 
    // Puedes agregar excepciones como: 
    // Route::get('eventos', [EventosController::class, 'index'])->middleware('role:admin,organizador,usuario');
    // -------------------------------------------------------------------

});


//route de auth
use App\Http\Controllers\AuthController;
Route::apiResource('auth', AuthController::class);

Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);

Route::middleware('auth:api')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
});

Route::middleware('auth:api')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
});