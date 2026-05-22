<?php

namespace App\Repository\Eloquent;

use App\Models\Recursos;
use App\Repository\Interfaces\RecursosInterfaces;
use Illuminate\Support\Facades\Cache;

class RecursosRepository implements RecursosInterfaces
{
    public function RecursosgetAll($perPage = null)
    {
        $page     = request()->query('page', 1);
        $limit    = $perPage ?? 15;
        $prefix   = $this->getListCachePrefix();
        $cacheKey = "{$prefix}list_p{$page}_l{$limit}";

        return Cache::remember($cacheKey, 60, function () use ($limit) {
            return Recursos::withCount('eventos')
                ->orderBy('created_at', 'desc')
                ->paginate($limit);
        });
    }

    public function RecursosgetById($id)
    {
        // Detalle individual: sí carga eventos asociados.
        return Recursos::with(['eventos:id,nombre,fecha_inicio,tipo_evento'])
            ->find($id);
    }

    public function Recursoscreate($data)
    {
        $recurso = Recursos::create($data);
        $this->flushListCache();
        return $recurso;
    }

    public function Recursosupdate($id, $data)
    {
        $affected = Recursos::where('id', $id)->update($data);
        if (! $affected) return null;
        $this->flushListCache();
        return Recursos::find($id);
    }

    public function Recursosdelete($id)
    {
        $deleted = (bool) Recursos::destroy($id);
        if ($deleted) $this->flushListCache();
        return $deleted;
    }

    private function getListCachePrefix(): string
    {
        return 'recursos_list_v' . Cache::rememberForever('recursos_list_version', fn () => 1) . '_';
    }

    private function flushListCache(): void
    {
        $version = Cache::rememberForever('recursos_list_version', fn () => 1);
        Cache::forever('recursos_list_version', $version + 1);
    }
}
