<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuariosRequest;
use App\Http\Resources\UsuariosResource;
use App\Services\UsuariosService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UsuariosController extends Controller
{
    protected $usuariosService;

    public function __construct(UsuariosService $usuariosService)
    {
        $this->usuariosService = $usuariosService;
    }

    public function index(Request $request)
    {
        $perPage  = $request->query('per_page') ?? null;
        $usuarios = $this->usuariosService->getAll($perPage);

        return UsuariosResource::collection($usuarios);
    }

    public function store(UsuariosRequest $request)
    {
        $data    = $request->validated();
        $usuario = $this->usuariosService->create($data);

        // FIX: asignar rol si viene en el request (no está en UsuariosRequest
        // porque es un campo extra manejado por Spatie, no por fillable)
        if ($request->filled('rol')) {
            $role = Role::where('name', strtolower($request->rol))
                        ->where('guard_name', 'api')
                        ->first();
            if ($role) {
                $usuario->assignRole($role);
            }
        }

        return (new UsuariosResource($usuario->load('roles')))
            ->response()
            ->setStatusCode(201);
    }

    public function show($id)
    {
        $usuario = $this->usuariosService->getById($id);
        if (! $usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return new UsuariosResource($usuario->load('roles'));
    }

    public function update(UsuariosRequest $request, $id)
    {
        $data    = $request->validated();
        $usuario = $this->usuariosService->update($id, $data);
        if (! $usuario) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        // FIX: actualizar rol si viene en el request
        if ($request->filled('rol')) {
            $role = Role::where('name', strtolower($request->rol))
                        ->where('guard_name', 'api')
                        ->first();
            if ($role) {
                $usuario->syncRoles([$role]);
            }
        }

        return new UsuariosResource($usuario->load('roles'));
    }

    public function destroy($id)
    {
        $deleted = $this->usuariosService->delete($id);
        if (! $deleted) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json(null, 204);
    }
}
