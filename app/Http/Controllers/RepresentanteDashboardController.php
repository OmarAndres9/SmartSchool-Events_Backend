<?php

namespace App\Http\Controllers;

use App\Http\Resources\EventosResource;
use App\Http\Resources\NotaResource;
use App\Models\User;
use App\Services\NotaService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class RepresentanteDashboardController extends Controller
{
    protected $notaService;

    public function __construct(NotaService $notaService)
    {
        $this->notaService = $notaService;
    }

    public function estudiantes()
    {
        $representante = JWTAuth::user();
        $estudiantes = $representante->estudiantesAsociados()
            ->get(['users.id', 'users.name', 'users.email']);

        return response()->json(['estudiantes' => $estudiantes]);
    }

    public function notasEstudiante($estudianteId, Request $request)
    {
        $this->verificarAsociacion($estudianteId);

        $periodoId = $request->query('periodo_id');
        $notas = $this->notaService->byEstudiante($estudianteId, $periodoId);

        return NotaResource::collection($notas);
    }

    public function promediosEstudiante($estudianteId, Request $request)
    {
        $this->verificarAsociacion($estudianteId);

        $periodoId = $request->query('periodo_id');
        $notas = $this->notaService->byEstudiante($estudianteId, $periodoId);

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

    public function eventosEstudiante($estudianteId)
    {
        $this->verificarAsociacion($estudianteId);

        $eventos = User::findOrFail($estudianteId)->eventosInscritos()
            ->wherePivot('estado', 'asistió')
            ->with(['recursos:id,nombre,tipo,ubicacion,estado'])
            ->orderByDesc('fecha_fin')
            ->get();

        return EventosResource::collection($eventos);
    }

    private function verificarAsociacion($estudianteId): void
    {
        $representante = JWTAuth::user();
        $asociado = $representante->estudiantesAsociados()
            ->where('users.id', $estudianteId)
            ->exists();

        if (! $asociado) {
            abort(403, 'No tienes acceso a los datos de este estudiante.');
        }
    }
}
