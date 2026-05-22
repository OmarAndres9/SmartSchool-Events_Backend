<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\User;
use App\Services\MateriaService;
use App\Services\NotaService;
use App\Services\PeriodoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Tymon\JWTAuth\Facades\JWTAuth;

class DocenteController extends Controller
{
    protected $materiaService;
    protected $notaService;
    protected $periodoService;

    public function __construct(
        MateriaService $materiaService,
        NotaService $notaService,
        PeriodoService $periodoService
    ) {
        $this->materiaService = $materiaService;
        $this->notaService = $notaService;
        $this->periodoService = $periodoService;
    }

    public function materias()
    {
        $docente = JWTAuth::user();
        $materias = $this->materiaService->getByDocente($docente->id);
        return response()->json(['materias' => $materias]);
    }

    public function estudiantes(Request $request)
    {
        $request->validate([
            'materia_id' => 'nullable|exists:materias,id',
            'periodo_id' => 'nullable|exists:periodos,id',
        ]);

        $estudiantes = User::role('estudiante')
            ->select('id', 'name', 'email', 'documento')
            ->orderBy('name')
            ->get();

        $notas = collect();
        if ($request->materia_id && $request->periodo_id) {
            $notas = Nota::where('materia_id', $request->materia_id)
                ->where('periodo_id', $request->periodo_id)
                ->get()
                ->keyBy('estudiante_id');
        }

        $result = $estudiantes->map(function ($est) use ($notas, $request) {
            $nota = $notas->get($est->id);
            return [
                'id'            => $est->id,
                'name'          => $est->name,
                'email'         => $est->email,
                'documento'     => $est->documento,
                'nota_id'       => $nota?->id,
                'calificacion'  => $nota?->calificacion,
                'registrado_por' => $nota?->registrado_por,
            ];
        });

        return response()->json(['estudiantes' => $result]);
    }

    public function guardarNota(Request $request)
    {
        $request->validate([
            'estudiante_id' => 'required|exists:users,id',
            'materia_id'    => 'required|exists:materias,id',
            'periodo_id'    => 'required|exists:periodos,id',
            'calificacion'  => 'required|numeric|min:0|max:5',
        ]);

        $docente = JWTAuth::user();

        $nota = Nota::updateOrCreate(
            [
                'estudiante_id' => $request->estudiante_id,
                'materia_id'    => $request->materia_id,
                'periodo_id'    => $request->periodo_id,
            ],
            [
                'calificacion'   => $request->calificacion,
                'registrado_por' => $docente->id,
            ]
        );

        $cacheKeys = [
            "notas_est_{$request->estudiante_id}_p{$request->periodo_id}",
            "dashboard_eventos_estudiante_{$request->estudiante_id}",
        ];
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        return response()->json([
            'message' => 'Nota guardada correctamente',
            'nota'    => $nota->load(['materia:id,nombre', 'periodo:id,nombre']),
        ]);
    }
}
