<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotificacionesRequest;
use App\Http\Resources\NotificacionesResource;
use App\Services\NotificacionesService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class NotificacionesController extends Controller
{
    protected $notificacionesService;

    public function __construct(NotificacionesService $notificacionesService)
    {
        $this->notificacionesService = $notificacionesService;
    }

    public function index(Request $request)
    {
        // FIX: solo paginar si se pide explícitamente; si no, devolver todo
        $perPage = $request->query('per_page') ?? null;

        return NotificacionesResource::collection(
            $this->notificacionesService->NotificacionesgetAll($perPage)
        );
    }

    public function show($id)
    {
        $notificacion = $this->notificacionesService->NotificacionesgetById($id);

        if (! $notificacion) {
            return response()->json(['message' => 'Notificación no encontrada'], 404);
        }

        return new NotificacionesResource($notificacion);
    }

    public function store(NotificacionesRequest $request)
    {
        $data = $request->validated();

        // FIX: asignar usuario autenticado automáticamente
        if (empty($data['id_usuario'])) {
            $data['id_usuario'] = JWTAuth::user()->id;
        }

        // FIX: asignar fecha_creacion automáticamente si no viene
        if (empty($data['fecha_creacion'])) {
            $data['fecha_creacion'] = now();
        }

        $notificacion = $this->notificacionesService->Notificacionescreate($data);

        return (new NotificacionesResource($notificacion))
            ->response()
            ->setStatusCode(201);
    }

    public function update(NotificacionesRequest $request, $id)
    {
        $notificacion = $this->notificacionesService->Notificacionesupdate($id, $request->validated());

        if (! $notificacion) {
            return response()->json(['message' => 'Notificación no encontrada'], 404);
        }

        return new NotificacionesResource($notificacion);
    }

    public function destroy($id)
    {
        $deleted = $this->notificacionesService->Notificacionesdelete($id);

        if (! $deleted) {
            return response()->json(['message' => 'Notificación no encontrada'], 404);
        }

        return response()->json(null, 204);
    }
}
