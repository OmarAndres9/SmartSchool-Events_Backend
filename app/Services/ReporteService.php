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

    public function getAllReportes()
    {
        return $this->reporteRepository->getAllReportes();
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
