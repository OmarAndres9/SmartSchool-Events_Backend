<?php

namespace App\Repository\Interfaces;
interface ReporteInterfaces
{
    public function getAllReportes();
    public function getReporteById($id);
    public function createReporte(array $data);
    public function updateReporte($id, array $data);
    public function deleteReporte($id);
}