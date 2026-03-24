<?php

namespace App\Repository\Eloquent;

use App\Models\Eventos;
use App\Repository\Interfaces\EventosInterfaces;

class EventosRepository implements EventosInterfaces
{
    public function EventosgetAll($perPage = null)
    {
        return $perPage ? Eventos::with('recursos')->paginate($perPage) : Eventos::with('recursos')->get();
    }

    public function EventosgetById($id)
    {
        return Eventos::find($id);
    }

    public function Eventoscreate($data)
    {
        return Eventos::create($data);
    }

    public function Eventosupdate($id, $data)
    {
        $model = Eventos::find($id);
        if (! $model) return null;
        $model->update($data);
        return $model;
    }

    public function Eventosdelete($id)
    {
        $model = Eventos::find($id);
        if (! $model) return false;
        $model->delete();
        return true;
    }

    // FIX: eventos del usuario autenticado
    public function EventosgetByUser($userId)
    {
        return Eventos::with('recursos')->where('creado_por', $userId)->orderBy('fecha_inicio')->get();
    }

    // FIX: eventos por tipo
    public function EventosgetByTipo($tipo)
    {
        return Eventos::with('recursos')->where('tipo_evento', $tipo)->orderBy('fecha_inicio')->get();
    }
}
