<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventosRequest;
use App\Http\Resources\EventosResource;
use App\Services\EventosService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class EventosController extends Controller
{
    protected $eventosService;

    public function __construct(EventosService $eventosService)
    {
        $this->eventosService = $eventosService;
    }

    public function index(Request $request)
    {
        $perPage = $request->query('per_page') ?? null;
        $eventos = $this->eventosService->getAll($perPage);

        return EventosResource::collection($eventos);
    }

    public function store(EventosRequest $request)
    {
        $data = $request->validated();

        // FIX: usar el usuario autenticado, no hardcodear id=1
        $data['creado_por'] = JWTAuth::user()->id;

        $evento = $this->eventosService->create($data);

        return (new EventosResource($evento))
            ->response()
            ->setStatusCode(201);
    }

    public function show($id)
    {
        $evento = $this->eventosService->getById($id);

        if (! $evento) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        // CORRECCIÓN: cargar recursos asignados al evento
        $evento->load('recursos');

        return new EventosResource($evento);
    }

    public function update(EventosRequest $request, $id)
    {
        $data = $request->validated();
        $evento = $this->eventosService->update($id, $data);

        if (! $evento) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        return new EventosResource($evento);
    }

    public function destroy($id)
    {
        $deleted = $this->eventosService->delete($id);

        if (! $deleted) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        return response()->json(null, 204);
    }

    public function asignarRecurso(Request $request, $id)
    {
        $evento = $this->eventosService->getById($id);

        if (! $evento) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        $request->validate([
            // CORRECCIÓN: campo unificado como recurso_id (consistente con el modelo)
            'recurso_id' => 'required|exists:_recursos__table,id',
            'cantidad'   => 'nullable|integer|min:1',
        ]);

        $evento->recursos()->syncWithoutDetaching([
            $request->recurso_id => ['cantidad' => $request->cantidad ?? 1],
        ]);

        $evento->load('recursos');

        return response()->json([
            'message' => 'Recurso asignado al evento correctamente',
            'evento'  => new EventosResource($evento),
        ]);
    }

    public function desasignarRecurso($id, $recursoId)
    {
        $evento = $this->eventosService->getById($id);

        if (! $evento) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        $evento->recursos()->detach($recursoId);

        return response()->json(['message' => 'Recurso desasignado del evento correctamente']);
    }

    // CORRECCIÓN: firma restaurada — se perdió en un refactor anterior
    public function misEventos()
    {
        $userId = JWTAuth::user()->id;
        $eventos = $this->eventosService->getByUser($userId);

        return EventosResource::collection($eventos);
    }

    public function getEventosByTipo($tipo)
    {
        $eventos = $this->eventosService->getByTipo($tipo);

        return EventosResource::collection($eventos);
    }
}
