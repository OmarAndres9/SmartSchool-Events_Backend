<?php

namespace App\Services;

use App\Repository\Interfaces\ReporteInterfaces;

class ReporteService
{
    protected $reporteRepository;

    public function __construct(ReporteInterfaces $reporteRepository)
    {
        $this->reporteRepository = $reporteRepository;
    }

    // CORRECCIÓN: propagar filtros desde el controller al repository
    public function getAllReportes(array $filtros = [])
    {
        return $this->reporteRepository->getAllReportes($filtros);
    }

    public function getReporteById($id)
    {
        return $this->reporteRepository->getReporteById($id);
    }

    public function createReporte(array $data)
    {
        return $this->reporteRepository->createReporte($data);
    }

    public function updateReporte($id, array $data)
    {
        return $this->reporteRepository->updateReporte($id, $data);
    }

    public function deleteReporte($id)
    {
        return $this->reporteRepository->deleteReporte($id);
    }
}
