<?php

namespace App\Repository\Eloquent;

use App\Models\Materia;
use App\Repository\Interfaces\MateriaInterfaces;
use Illuminate\Support\Facades\Cache;

class MateriaRepository implements MateriaInterfaces
{
    public function MateriagetAll($perPage = null)
    {
        $page     = request()->query('page', 1);
        $limit    = $perPage ?? 15;
        $prefix   = $this->getListCachePrefix();
        $cacheKey = "{$prefix}list_p{$page}_l{$limit}";

        return Cache::remember($cacheKey, 60, function () use ($limit) {
            return Materia::with('docente:id,name')
                ->orderBy('nombre')
                ->paginate($limit);
        });
    }

    public function MateriagetById($id)
    {
        return Materia::with('docente:id,name')->find($id);
    }

    public function Materiacreate($data)
    {
        $materia = Materia::create($data);
        $this->flushListCache();
        return $materia;
    }

    public function Materiaupdate($id, $data)
    {
        $affected = Materia::where('id', $id)->update($data);
        if (! $affected) return null;
        $this->flushListCache();
        return Materia::find($id);
    }

    public function Materiadelete($id)
    {
        $deleted = (bool) Materia::destroy($id);
        if ($deleted) $this->flushListCache();
        return $deleted;
    }

    public function MateriagetByDocente($docenteId)
    {
        return Materia::where('docente_id', $docenteId)
            ->orderBy('nombre')
            ->get();
    }

    private function getListCachePrefix(): string
    {
        return 'materias_list_v' . Cache::rememberForever('materias_list_version', fn () => 1) . '_';
    }

    private function flushListCache(): void
    {
        $version = Cache::rememberForever('materias_list_version', fn () => 1);
        Cache::forever('materias_list_version', $version + 1);
    }
}
