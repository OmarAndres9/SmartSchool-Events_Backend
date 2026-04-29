<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuariosRequest;
use App\Http\Resources\UsuariosResource;
use App\Services\UsuariosService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Tymon\JWTAuth\Facades\JWTAuth;

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

        return UsuariosResource::collection($usuarios)
            ->response()
            ->withHeaders([
                'Cache-Control' => 'public, max-age=30, stale-while-revalidate=60',
            ]);
    }

    public function store(UsuariosRequest $request)
    {
        $data    = $request->validated();
        $usuario = $this->usuariosService->create($data);

        if ($request->filled('rol')) {
            $role = Role::where('name', strtolower($request->rol))
                        ->where('guard_name', 'api')
                        ->first();
            if ($role) $usuario->assignRole($role);
        }

        return (new UsuariosResource($usuario->load('roles')))
            ->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $usuario = $this->usuariosService->getById($id);
        if (!$usuario) return response()->json(['message' => 'Usuario no encontrado'], 404);
        return new UsuariosResource($usuario->load('roles'));
    }

    public function update(UsuariosRequest $request, $id)
    {
        $usuarioObjetivo = $this->usuariosService->getById($id);
        if (!$usuarioObjetivo) return response()->json(['message' => 'Usuario no encontrado'], 404);

        $adminActual = JWTAuth::user();
        $esOtroAdmin = $usuarioObjetivo->hasRole('admin') && $adminActual->id !== $usuarioObjetivo->id;

        if ($esOtroAdmin) {
            return response()->json(['message' => 'No tienes permiso para editar a otro administrador.'], 403);
        }

        $data    = $request->validated();
        $usuario = $this->usuariosService->update($id, $data);

        if ($request->filled('rol')) {
            $role = Role::where('name', strtolower($request->rol))
                        ->where('guard_name', 'api')
                        ->first();
            if ($role) {
                $usuario->syncRoles([$role]);
                // OPTIMIZACIÓN: invalidar caché de roles al cambiarlos
                Cache::forget("user_roles_{$id}");
            }
        }

        return new UsuariosResource($usuario->load('roles'));
    }

    public function destroy($id)
    {
        $usuarioObjetivo = $this->usuariosService->getById($id);
        if (!$usuarioObjetivo) return response()->json(['message' => 'Usuario no encontrado'], 404);

        $adminActual = JWTAuth::user();
        $esOtroAdmin = $usuarioObjetivo->hasRole('admin') && $adminActual->id !== $usuarioObjetivo->id;

        if ($esOtroAdmin) {
            return response()->json(['message' => 'No tienes permiso para eliminar a otro administrador.'], 403);
        }

        $this->usuariosService->delete($id);
        Cache::forget("user_roles_{$id}");
        return response()->json(null, 204);
    }
}
