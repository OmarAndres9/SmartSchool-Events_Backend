<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventosRequest;
use App\Http\Requests\InscripcionRequest;
use App\Http\Resources\EventosResource;
use App\Models\Recursos;
use App\Models\User;
use App\Services\EventosService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

        // OPTIMIZACIÓN: Cache-Control con stale-while-revalidate.
        // El browser sirve la respuesta anterior mientras valida en background.
        return EventosResource::collection($eventos)
            ->response()
            ->withHeaders([
                'Cache-Control' => 'public, max-age=30, stale-while-revalidate=60',
            ]);
    }

    public function store(EventosRequest $request)
    {
        $data = $request->validated();
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
        $data   = $request->validated();
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
            'recurso_id' => 'required|exists:_recursos__table,id',
            'cantidad'   => 'nullable|integer|min:1',
        ]);

        $evento->recursos()->syncWithoutDetaching([
            $request->recurso_id => ['cantidad' => $request->cantidad ?? 1],
        ]);

        Recursos::where('id', $request->recurso_id)->update(['estado' => 'ocupado']);

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

        $recurso = Recursos::find($recursoId);
        if ($recurso && $recurso->eventos()->count() === 0) {
            $recurso->update(['estado' => 'disponible']);
        }

        return response()->json(['message' => 'Recurso desasignado del evento correctamente']);
    }

    public function misEventos()
    {
        $userId  = JWTAuth::user()->id;
        $eventos = $this->eventosService->getByUser($userId);

        return EventosResource::collection($eventos);
    }

    public function getEventosByTipo($tipo)
    {
        $eventos = $this->eventosService->getByTipo($tipo);

        return EventosResource::collection($eventos);
    }

    public function inscribir(InscripcionRequest $request, $id)
    {
        $evento = $this->eventosService->getById($id);

        if (! $evento) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        $userId = $request->user_id ?? JWTAuth::user()->id;

        $evento->inscripciones()->syncWithoutDetaching([$userId]);

        Cache::forget("mis_inscripciones_{$userId}");

        return response()->json([
            'message' => 'Inscripción exitosa',
            'inscritos' => $evento->inscripciones()->count(),
        ]);
    }

    public function desinscribir($id)
    {
        $evento = $this->eventosService->getById($id);

        if (! $evento) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        $userId = JWTAuth::user()->id;
        $evento->inscripciones()->detach($userId);

        Cache::forget("mis_inscripciones_{$userId}");

        return response()->json([
            'message' => 'Inscripción cancelada',
        ]);
    }

    public function inscritos($id)
    {
        $evento = $this->eventosService->getById($id);

        if (! $evento) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        $inscritos = $evento->inscripciones()->get(['users.id', 'users.name', 'users.email']);

        return response()->json([
            'inscritos' => $inscritos,
            'total'     => $inscritos->count(),
        ]);
    }

    public function misInscripciones()
    {
        $userId = JWTAuth::user()->id;

        $eventos = Cache::remember("mis_inscripciones_{$userId}", 60, function () use ($userId) {
            return User::find($userId)?->eventosInscritos()
                ->with(['recursos:id,nombre,tipo,ubicacion,estado'])
                ->orderBy('fecha_inicio')
                ->get() ?? collect();
        });

        return EventosResource::collection($eventos);
    }
}
