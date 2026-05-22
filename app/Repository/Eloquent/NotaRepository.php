<?php

namespace App\Repository\Eloquent;

use App\Models\Nota;
use App\Repository\Interfaces\NotaInterfaces;
use Illuminate\Support\Facades\Cache;

class NotaRepository implements NotaInterfaces
{
    public function NotagetAll($perPage = null)
    {
        $page     = request()->query('page', 1);
        $limit    = $perPage ?? 15;
        $prefix   = $this->getListCachePrefix();
        $cacheKey = "{$prefix}list_p{$page}_l{$limit}";

        return Cache::remember($cacheKey, 60, function () use ($limit) {
            return Nota::with(['materia:id,nombre', 'periodo:id,nombre', 'estudiante:id,name'])
                ->orderByDesc('created_at')
                ->paginate($limit);
        });
    }

    public function NotagetById($id)
    {
        return Nota::with(['materia', 'periodo', 'estudiante', 'registradoPor'])->find($id);
    }

    public function Notacreate($data)
    {
        $nota = Nota::create($data);
        $this->flushListCache();
        return $nota;
    }

    public function Notaupdate($id, $data)
    {
        $affected = Nota::where('id', $id)->update($data);
        if (! $affected) return null;
        $this->flushListCache();
        return Nota::find($id);
    }

    public function Notadelete($id)
    {
        $deleted = (bool) Nota::destroy($id);
        if ($deleted) $this->flushListCache();
        return $deleted;
    }

    public function NotabyEstudiante($estudianteId, $periodoId = null)
    {
        return Cache::remember("notas_est_{$estudianteId}_p{$periodoId}", 60, function () use ($estudianteId, $periodoId) {
            $query = Nota::with(['materia', 'periodo'])
                ->where('estudiante_id', $estudianteId);

            if ($periodoId) {
                $query->where('periodo_id', $periodoId);
            }

            return $query->orderBy('materia_id')->get();
        });
    }

    private function getListCachePrefix(): string
    {
        return 'notas_list_v' . Cache::rememberForever('notas_list_version', fn () => 1) . '_';
    }

    private function flushListCache(): void
    {
        $version = Cache::rememberForever('notas_list_version', fn () => 1);
        Cache::forever('notas_list_version', $version + 1);
    }
}
