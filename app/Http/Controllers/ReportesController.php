<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReporteRequest;
use App\Http\Resources\ReporteResource;
use App\Services\ReporteService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class ReportesController extends Controller
{
    // FIX: usaba ReporteInterfaces directamente — saltaba la capa Service
    protected $reporteService;

    public function __construct(ReporteService $reporteService)
    {
        $this->reporteService = $reporteService;
    }

    public function index(Request $request)
    {
        // CORRECCIÓN: el frontend envía fecha_inicio, fecha_fin, tipo, estado
        // como query params — se propagan al service y repository para filtrar
        $filtros = $request->only(['fecha_inicio', 'fecha_fin', 'tipo', 'estado']);
        $filtros = array_filter($filtros, fn($v) => $v !== '' && $v !== null);

        $reportes = $this->reporteService->getAllReportes($filtros);
        return ReporteResource::collection($reportes);
    }

    public function store(ReporteRequest $request)
    {
        $data = $request->validated();

        // FIX: asignar automáticamente el usuario autenticado si no viene en el request
        if (empty($data['id_usuario'])) {
            $data['id_usuario'] = JWTAuth::user()->id;
        }

        $reporte = $this->reporteService->createReporte($data);
        return (new ReporteResource($reporte))->response()->setStatusCode(201);
    }

    public function show($id)
    {
        $reporte = $this->reporteService->getReporteById($id);
        if (! $reporte) {
            return response()->json(['message' => 'Reporte no encontrado'], 404);
        }
        return new ReporteResource($reporte);
    }

    public function update(ReporteRequest $request, $id)
    {
        $data = $request->validated();
        $reporte = $this->reporteService->updateReporte($id, $data);
        if (! $reporte) {
            return response()->json(['message' => 'Reporte no encontrado'], 404);
        }
        return new ReporteResource($reporte);
    }

    public function destroy($id)
    {
        $deleted = $this->reporteService->deleteReporte($id);
        if (! $deleted) {
            return response()->json(['message' => 'Reporte no encontrado'], 404);
        }
        return response()->json(null, 204);
    }
}
