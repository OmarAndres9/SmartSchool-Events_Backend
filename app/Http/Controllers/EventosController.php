<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventosRequest;
use App\Http\Requests\InscripcionRequest;
use App\Http\Resources\EventosResource;
use App\Models\Eventos;
use App\Models\User;
use App\Notifications\EventoCreada;
use App\Services\EventosService;
use App\Services\RecursosService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Tymon\JWTAuth\Facades\JWTAuth;

class EventosController extends Controller
{
    protected $eventosService;
    protected $recursosService;

    public function __construct(EventosService $eventosService, RecursosService $recursosService)
    {
        $this->eventosService = $eventosService;
        $this->recursosService = $recursosService;
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

        User::role(['estudiante', 'docente'])->chunk(100, function ($users) use ($evento) {
            foreach ($users as $user) {
                $user->notify(new EventoCreada($evento));
            }
        });

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

        User::role(['estudiante', 'docente'])->chunk(100, function ($users) use ($evento) {
            foreach ($users as $user) {
                $user->notify(new EventoCreada($evento, 'actualizado'));
            }
        });

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

        $this->recursosService->Recursosupdate($request->recurso_id, ['estado' => 'ocupado']);

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

        $recurso = $this->recursosService->RecursosgetById($recursoId);
        if ($recurso && $recurso->eventos()->count() === 0) {
            $this->recursosService->Recursosupdate($recursoId, ['estado' => 'disponible']);
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
        $estado = $request->estado ?? 'pendiente';

        $evento->inscripciones()->syncWithoutDetaching([
            $userId => ['estado' => $estado],
        ]);

        if ($request->user_id && $request->user_id != JWTAuth::user()->id) {
            Cache::forget("mis_inscripciones_{$request->user_id}");
        }
        Cache::forget("mis_inscripciones_" . JWTAuth::user()->id);

        $evento->load('inscripciones');

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

        $inscritos = $evento->inscripciones()->get(['users.id', 'users.name', 'users.email'])->map(fn($u) => [
            'id'     => $u->id,
            'name'   => $u->name,
            'email'  => $u->email,
            'estado' => $u->pivot->estado ?? 'pendiente',
        ]);

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

    public function subirArchivo(Request $request, $id)
    {
        $evento = $this->eventosService->getById($id);

        if (! $evento) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        $request->validate([
            'archivo' => 'required|file|max:10240',
        ]);

        $file = $request->file('archivo');
        $ruta = $file->store("eventos/{$id}", 'public');

        $archivo = $evento->archivos()->create([
            'nombre_original' => $file->getClientOriginalName(),
            'ruta'            => $ruta,
            'tipo'            => $file->getClientMimeType(),
            'tamano'          => $file->getSize(),
        ]);

        return response()->json([
            'message' => 'Archivo subido correctamente',
            'archivo' => $archivo,
        ], 201);
    }

    public function eliminarArchivo($id, $archivoId)
    {
        $evento = $this->eventosService->getById($id);

        if (! $evento) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        $archivo = $evento->archivos()->find($archivoId);

        if (! $archivo) {
            return response()->json(['message' => 'Archivo no encontrado'], 404);
        }

        \Illuminate\Support\Facades\Storage::disk('public')->delete($archivo->ruta);

        $archivo->delete();

        return response()->json(['message' => 'Archivo eliminado']);
    }

    public function archivos($id)
    {
        $evento = $this->eventosService->getById($id);

        if (! $evento) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        return response()->json([
            'archivos' => $evento->archivos,
        ]);
    }

    public function instancias($id)
    {
        $evento = $this->eventosService->getById($id);

        if (! $evento) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        $instancias = $evento->instancias()
            ->with(['recursos:id,nombre,tipo,ubicacion,estado'])
            ->withCount('inscripciones')
            ->orderBy('fecha_inicio')
            ->get();

        return EventosResource::collection($instancias);
    }

    // ── FAVORITOS ──────────────────────────────────────────────────────────────

    public function marcarFavorito($id)
    {
        $evento = $this->eventosService->getById($id);

        if (! $evento) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        $userId = JWTAuth::user()->id;
        $evento->favoritos()->syncWithoutDetaching([$userId]);

        return response()->json(['message' => 'Evento marcado como favorito']);
    }

    public function desmarcarFavorito($id)
    {
        $evento = $this->eventosService->getById($id);

        if (! $evento) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        $evento->favoritos()->detach(JWTAuth::user()->id);

        return response()->json(['message' => 'Favorito eliminado']);
    }

    public function favoritos()
    {
        $userId = JWTAuth::user()->id;
        $eventos = User::find($userId)?->eventosFavoritos()
            ->with(['recursos:id,nombre,tipo,ubicacion,estado'])
            ->withCount('inscripciones')
            ->orderBy('fecha_inicio')
            ->get() ?? collect();

        return EventosResource::collection($eventos);
    }

    // ── VALORACIONES ───────────────────────────────────────────────────────────

    public function valorar(Request $request, $id)
    {
        $evento = $this->eventosService->getById($id);

        if (! $evento) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        $request->validate([
            'puntuacion' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:1000',
        ]);

        $userId = JWTAuth::user()->id;

        $evento->valoraciones()->updateOrCreate(
            ['user_id' => $userId],
            ['puntuacion' => $request->puntuacion, 'comentario' => $request->comentario]
        );

        return response()->json([
            'message' => 'Valoración guardada',
            'rating_promedio' => round($evento->ratingPromedio(), 1),
            'total' => $evento->valoraciones()->count(),
        ]);
    }

    public function valoraciones($id)
    {
        $evento = $this->eventosService->getById($id);

        if (! $evento) {
            return response()->json(['message' => 'Evento no encontrado'], 404);
        }

        $valoraciones = $evento->valoraciones()
            ->with('user:id,name')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'rating_promedio' => round($evento->ratingPromedio(), 1),
            'total'           => $valoraciones->count(),
            'valoraciones'    => $valoraciones,
        ]);
    }

    // ── CALENDARIO ─────────────────────────────────────────────────────────────

    public function calendario(Request $request)
    {
        $mes = $request->query('mes', now()->format('Y-m'));

        $eventos = Eventos::whereNull('evento_origen_id')
            ->whereYear('fecha_inicio', substr($mes, 0, 4))
            ->whereMonth('fecha_inicio', substr($mes, 5, 2))
            ->with(['recursos:id,nombre,tipo,ubicacion,estado'])
            ->withCount('inscripciones')
            ->orderBy('fecha_inicio')
            ->get()
            ->groupBy(fn($e) => $e->fecha_inicio->format('Y-m-d'));

        return response()->json([
            'mes'     => $mes,
            'dias'    => $eventos->map(fn($d, $fecha) => [
                'fecha'   => $fecha,
                'eventos' => EventosResource::collection($d),
            ])->values(),
        ]);
    }

    // ── HISTORIAL ──────────────────────────────────────────────────────────────

    public function historial()
    {
        $userId = JWTAuth::user()->id;

        $eventos = User::find($userId)?->eventosInscritos()
            ->wherePivot('estado', 'asistió')
            ->where('fecha_fin', '<', now())
            ->with(['recursos:id,nombre,tipo,ubicacion,estado'])
            ->orderByDesc('fecha_fin')
            ->get() ?? collect();

        return EventosResource::collection($eventos);
    }

    public function proximos()
    {
        $userId = JWTAuth::user()->id;

        $eventos = User::find($userId)?->eventosInscritos()
            ->where('fecha_inicio', '>', now())
            ->with(['recursos:id,nombre,tipo,ubicacion,estado'])
            ->withCount('inscripciones')
            ->orderBy('fecha_inicio')
            ->get() ?? collect();

        return EventosResource::collection($eventos);
    }
}
