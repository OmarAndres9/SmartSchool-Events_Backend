<?php

namespace App\Repository\Eloquent;

use App\Models\Periodo;
use App\Repository\Interfaces\PeriodoInterfaces;
use Illuminate\Support\Facades\Cache;

class PeriodoRepository implements PeriodoInterfaces
{
    public function PeriodogetAll($perPage = null)
    {
        $page     = request()->query('page', 1);
        $limit    = $perPage ?? 15;
        $prefix   = $this->getListCachePrefix();
        $cacheKey = "{$prefix}list_p{$page}_l{$limit}";

        return Cache::remember($cacheKey, 60, function () use ($limit) {
            return Periodo::orderByDesc('fecha_inicio')->paginate($limit);
        });
    }

    public function PeriodogetById($id)
    {
        return Periodo::find($id);
    }

    public function Periodocreate($data)
    {
        $periodo = Periodo::create($data);
        $this->flushListCache();
        return $periodo;
    }

    public function Periodoupdate($id, $data)
    {
        $affected = Periodo::where('id', $id)->update($data);
        if (! $affected) return null;
        $this->flushListCache();
        return Periodo::find($id);
    }

    public function Periododelete($id)
    {
        $deleted = (bool) Periodo::destroy($id);
        if ($deleted) $this->flushListCache();
        return $deleted;
    }

    public function Periodoactivo()
    {
        return Cache::remember('periodo_activo', 60, function () {
            return Periodo::where('activo', true)->first();
        });
    }

    private function getListCachePrefix(): string
    {
        return 'periodos_list_v' . Cache::rememberForever('periodos_list_version', fn () => 1) . '_';
    }

    private function flushListCache(): void
    {
        $version = Cache::rememberForever('periodos_list_version', fn () => 1);
        Cache::forever('periodos_list_version', $version + 1);
    }
}
