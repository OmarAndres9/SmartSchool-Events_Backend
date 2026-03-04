<?php

namespace App\Repository\Eloquent;
use App\Repository\Interfaces\ReporteInterfaces;
use App\Models\Reporte;

class ReporteRepository implements ReporteInterfaces
{
    public function getAllReportes()
    {
        return Reporte::all();
    }
    public function getReporteById($id)
    {
        return Reporte::find($id);
    }
    public function createReporte(array $data)
    {
        return Reporte::create($data);
    }
    public function updateReporte($id, array $data)
    {
        return Reporte::where('id', $id)->update($data);
    }
    public function deleteReporte($id)
    {
        return Reporte::where('id', $id)->delete();
    }
}