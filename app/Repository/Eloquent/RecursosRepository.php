<?php

namespace App\Repository\Eloquent;

use App\Models\Recursos;
use App\Repository\Interfaces\RecursosInterfaces;

class RecursosRepository implements RecursosInterfaces
{
    public function RecursosgetAll($perPage = null)
    {
        // OPTIMIZACIÓN: no cargar todos los eventos de cada recurso en el listado
        // (causaba un N+1 severo al listar recursos). Solo contamos los eventos.
        $query = Recursos::withCount('eventos');

        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    public function RecursosgetById($id)
    {
        // Detalle individual sí carga los eventos asociados con columnas mínimas
        return Recursos::with(['eventos:id,nombre,fecha_inicio,tipo_evento'])
            ->find($id);
    }

    public function Recursoscreate($data)
    {
        return Recursos::create($data);
    }

    public function Recursosupdate($id, $data)
    {
        $affected = Recursos::where('id', $id)->update($data);
        if (! $affected) return null;
        return Recursos::find($id);
    }

    public function Recursosdelete($id)
    {
        return (bool) Recursos::destroy($id);
    }
}

