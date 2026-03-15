<?php

namespace App\Repository\Eloquent;

use App\Models\Reporte;
use App\Repository\Interfaces\ReporteInterfaces;

class ReporteRepository implements ReporteInterfaces
{
    public function getAllReportes()
    {
        return Reporte::orderBy('created_at', 'desc')->get();
    }

    public function getReporteById($id)
    {
        return Reporte::find($id);
    }

    public function createReporte(array $data)
    {
        return Reporte::create($data);
    }

    // FIX: retornaba int (filas afectadas), no el modelo — rompía el Resource
    public function updateReporte($id, array $data)
    {
        $model = Reporte::find($id);
        if (! $model) return null;
        $model->update($data);
        return $model;
    }

    // FIX: retornaba int, no bool — rompía el if(!$deleted) del controller
    public function deleteReporte($id)
    {
        $model = Reporte::find($id);
        if (! $model) return false;
        $model->delete();
        return true;
    }
}
