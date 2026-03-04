<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\ReporteResource;
use App\Http\Requests\ReporteRequest;
use App\Repository\Interfaces\ReporteInterfaces;


class ReportesController extends Controller
{
     protected $reporteRepository;
    public function __construct(ReporteInterfaces $reporteRepository)
    {
        $this->reporteRepository = $reporteRepository;

}
  
    public function index()
    {
        $reportes = $this->reporteRepository->getAllReportes();
        return ReporteResource::collection($reportes);
    }

    public function store(ReporteRequest $request)
    {
        $data = $request->validated();
        $reporte = $this->reporteRepository->createReporte($data);
        return new ReporteResource($reporte);
    }

    public function show($id)
    {
        $reporte = $this->reporteRepository->getReporteById($id);
        if (!$reporte) {
            return response()->json(['message' => 'Reporte no encontrado'], 404);
        }
        return new ReporteResource($reporte);
    }

    public function update(ReporteRequest $request, $id)
    {
        $data = $request->validated();
        $reporte = $this->reporteRepository->updateReporte($id, $data);
        if (!$reporte) {
            return response()->json(['message' => 'Reporte no encontrado'], 404);
        }
        return new ReporteResource($reporte);
    }

    public function destroy($id)
    {
        $deleted = $this->reporteRepository->deleteReporte($id);
        if (!$deleted) {
            return response()->json(['message' => 'Reporte no encontrado'], 404);
        }
        return response()->json(['message' => 'Reporte eliminado correctamente']);
    }
}
