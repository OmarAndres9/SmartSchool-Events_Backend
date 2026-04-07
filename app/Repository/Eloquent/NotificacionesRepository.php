<?php

namespace App\Repository\Eloquent;

use App\Models\Notificaciones;
use App\Repository\Interfaces\NotificacionesInterfaces;

class NotificacionesRepository implements NotificacionesInterfaces
{
    public function NotificacionesgetAll($perPage = null)
    {
        // OPTIMIZACIÓN: ordenar por más recientes primero; paginar siempre que sea posible
        $query = Notificaciones::orderByDesc('created_at');

        return $query->paginate($perPage ?? 15); // Siempre pagina con 15 por defecto
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
        $affected = Notificaciones::where('id', $id)->update($data);
        if (! $affected) return null;
        return Notificaciones::find($id);
    }

    public function Notificacionesdelete($id)
    {
        return (bool) Notificaciones::destroy($id);
    }
}

