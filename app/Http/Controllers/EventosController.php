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

    // FIX: endpoint mis-eventos — filtra por usuario autenticado
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
