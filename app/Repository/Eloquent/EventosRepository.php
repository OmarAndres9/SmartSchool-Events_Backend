<?php

namespace App\Repository\Eloquent;
use App\Repository\Interfaces\EventosInterfaces;
use App\Models\Eventos;

class EventosRepository implements EventosInterfaces
{
    public function EventosgetAll($perPage = null)
    {
        return $perPage ? Eventos::paginate($perPage) : Eventos::all();
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
        if (!$model) return null;
        $model->update($data);
        return $model;
    }

    public function Eventosdelete($id)
    {
        $model = Eventos::find($id);
        if (!$model) return false;
        $model->delete();
        return true;
    }
}


