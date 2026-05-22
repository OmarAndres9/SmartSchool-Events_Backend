<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventosResource;
use App\Http\Resources\NotaResource;
use App\Services\NotaService;
use App\Services\PeriodoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Tymon\JWTAuth\Facades\JWTAuth;

class EstudianteDashboardController extends Controller
{
    protected $notaService;
    protected $periodoService;

    public function __construct(NotaService $notaService, PeriodoService $periodoService)
    {
        $this->notaService = $notaService;
        $this->periodoService = $periodoService;
    }

    public function notas(Request $request)
    {
        $estudiante = JWTAuth::user();
        $periodoId  = $request->query('periodo_id');

        $notas = $this->notaService->byEstudiante($estudiante->id, $periodoId);

        return NotaResource::collection($notas);
    }

    public function promedios(Request $request)
    {
        $estudiante = JWTAuth::user();
        $periodoId  = $request->query('periodo_id');

        $notas = $this->notaService->byEstudiante($estudiante->id, $periodoId);

        $promedios = $notas->groupBy('materia_id')->map(function ($notasMateria) {
            $materia = $notasMateria->first()->materia;
            return [
                'materia_id'  => $materia->id,
                'materia'     => $materia->nombre,
                'promedio'    => round($notasMateria->avg('calificacion'), 2),
                'total_notas' => $notasMateria->count(),
            ];
        })->values();

        return response()->json([
            'promedio_general' => round($notas->avg('calificacion'), 2),
            'materias'         => $promedios,
        ]);
    }

    public function eventos()
    {
        $estudiante = JWTAuth::user();

        $eventos = Cache::remember("dashboard_eventos_estudiante_{$estudiante->id}", 60, function () {
            return \App\Models\Eventos::whereNull('evento_origen_id')
                ->whereIn('visibilidad', ['publico', 'institucional'])
                ->with(['recursos:id,nombre,tipo,ubicacion,estado'])
                ->withCount(['inscripciones', 'favoritos'])
                ->orderBy('fecha_inicio')
                ->get();
        });

        return EventosResource::collection($eventos);
    }

    public function periodos()
    {
        $periodos = $this->periodoService->getAll(100);
        return response()->json(['periodos' => $periodos->items()]);
    }
}
