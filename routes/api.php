<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;

Route::apiResource('roles', RoleController::class);
Route::apiResource('permissions', PermissionController::class);





// Ejemplo de endpoint para asignar un rol a un usuario
Route::post('users/{user}/roles', function (Request $request, \App\Models\User $user) {
    $request->validate([
        'roles' => 'required|array'
    ]);

    // SyncRoles le quita los roles anteriores y le asigna los nuevos enviados en el array
    $user->syncRoles($request->roles);

    return response()->json([
        'message' => 'Roles asignados correctamente',
        'user' => $user->load('roles')
    ]);
});



//rutas de recursos
use App\Http\Controllers\RecursosController;
Route::apiResource('recursos', RecursosController::class);


//route de usuarios
use App\Http\Controllers\UsuariosController;
Route::apiResource('usuarios', UsuariosController::class);

//route de eventos
use App\Http\Controllers\EventosController;
Route::apiResource('eventos', EventosController::class);

//route de reportes
use App\Http\Controllers\ReportesController;
Route::apiResource('reportes', ReportesController::class);