<?php

namespace App\Repository\Eloquent;

use App\Models\Notificaciones;
use App\Repository\Interfaces\NotificacionesInterfaces;
use Illuminate\Support\Facades\Cache;

class NotificacionesRepository implements NotificacionesInterfaces
{
    public function NotificacionesgetAll($perPage = null)
    {
        $page     = request()->query('page', 1);
        $limit    = $perPage ?? 15;
        $cacheKey = "notificaciones_list_p{$page}_l{$limit}";

        return Cache::remember($cacheKey, 60, function () use ($limit) {
            return Notificaciones::orderByDesc('created_at')->paginate($limit);
        });
    }

    public function NotificacionesgetById($id)
    {
        return Notificaciones::find($id);
    }

    public function Notificacionescreate($data)
    {
        $notif = Notificaciones::create($data);
        $this->flushListCache();
        return $notif;
    }

    public function Notificacionesupdate($id, $data)
    {
        $affected = Notificaciones::where('id', $id)->update($data);
        if (! $affected) return null;
        $this->flushListCache();
        return Notificaciones::find($id);
    }

    public function Notificacionesdelete($id)
    {
        $deleted = (bool) Notificaciones::destroy($id);
        if ($deleted) $this->flushListCache();
        return $deleted;
    }

    private function flushListCache(): void
    {
        for ($p = 1; $p <= 5; $p++) {
            Cache::forget("notificaciones_list_p{$p}_l15");
        }
    }
}
