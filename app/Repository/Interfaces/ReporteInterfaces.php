<?php

namespace App\Repository\Interfaces;

interface ReporteInterfaces
{
    // CORRECCIÓN: firma actualizada para aceptar filtros desde el frontend
    public function getAllReportes(array $filtros = []);

    public function getReporteById($id);

    public function createReporte(array $data);

    public function updateReporte($id, array $data);

    public function deleteReporte($id);
}
