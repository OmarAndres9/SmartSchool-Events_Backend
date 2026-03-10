<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventosRequest;
use App\Http\Resources\EventosResource;
use App\Services\EventosService;
use Illuminate\Http\Request;

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

        // 👇 Asignamos el usuario creador
        $data['creado_por'] = 1; // Cambia por auth()->id() si usas autenticación

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

    public function getEventosByTipo($tipo)
    {
        $eventos = $this->eventosService->getByTipo($tipo);

        return EventosResource::collection($eventos);
    }
}
