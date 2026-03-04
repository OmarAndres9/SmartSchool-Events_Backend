<?php
namespace App\Repository\Eloquent;
use App\Models\Notificaciones;
use App\Repository\Interfaces\NotificacionesInterfaces;

class NotificacionesRepository implements NotificacionesInterfaces
{
    public function NotificacionesgetAll($perPage = null)
    {
        return $perPage ? Notificaciones::paginate($perPage) : Notificaciones::all();
    }

    public function NotificacionesgetById($id)
    {
        return Notificaciones::find($id);
    }

    public function Notificacionescreate($data)
    {
        return Notificaciones::create($data);
    }

    public function Notificacionesupdate($id, $data)
    {
        $model = Notificaciones::find($id);
        if (!$model) return null;
        $model->update($data);
        return $model;
    }

    public function Notificacionesdelete($id)
    {
        $model = Notificaciones::find($id);
        if (!$model) return false;
        $model->delete();
        return true;
    }
}