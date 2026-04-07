<?php

namespace App\Repository\Eloquent;

use App\Models\Reporte;
use App\Repository\Interfaces\ReporteInterfaces;

class ReporteRepository implements ReporteInterfaces
{
    // CORRECCIÓN: el frontend envía fecha_inicio, fecha_fin, tipo, estado como query params
    // El controller los ignoraba — ahora se filtran en la consulta
    public function getAllReportes(array $filtros = [])
    {
        $query = Reporte::orderBy('created_at', 'desc');

        if (!empty($filtros['fecha_inicio'])) {
            $query->where('fecha', '>=', $filtros['fecha_inicio']);
        }
        if (!empty($filtros['fecha_fin'])) {
            $query->where('fecha', '<=', $filtros['fecha_fin']);
        }
        if (!empty($filtros['tipo'])) {
            $query->where('tipo', $filtros['tipo']);
        }
        if (!empty($filtros['estado'])) {
            $query->where('estado', $filtros['estado']);
        }

        // OPTIMIZACIÓN: usar paginación en lugar de get() para evitar cargar todos los registros
        // El recurso ReporteResource no necesita relaciones adicionales, por lo que no cargamos eager loads innecesarios.
        return $query->paginate(15); // 15 por página
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
        $model = Reporte::find($id);
        if (! $model) return null;
        $model->update($data);
        return $model;
    }

    public function deleteReporte($id)
    {
        $model = Reporte::find($id);
        if (! $model) return false;
        $model->delete();
        return true;
    }
}
