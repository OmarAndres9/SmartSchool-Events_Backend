<?php

namespace App\Repository\Eloquent;

use App\Models\Eventos;
use App\Repository\Interfaces\EventosInterfaces;
use Illuminate\Support\Facades\Cache;

class EventosRepository implements EventosInterfaces
{
    /**
     * Columnas base para listados (excluye TEXT largo como descripcion).
     */
    private const LIST_COLUMNS = [
        'id', 'nombre', 'fecha_inicio', 'fecha_fin',
        'lugar', 'tipo_evento', 'modalidad', 'grupo_destinado', 'creado_por',
        'created_at', 'updated_at',
    ];

    public function EventosgetAll($perPage = null)
    {
        $page      = request()->query('page', 1);
        $limit     = $perPage ?? 15;
        $prefix    = $this->getListCachePrefix();
        $cacheKey  = "{$prefix}list_p{$page}_l{$limit}";

        return Cache::remember($cacheKey, 60, function () use ($limit) {
            return Eventos::select(self::LIST_COLUMNS)
                ->orderBy('created_at', 'desc')
                ->paginate($limit);
        });
    }

    public function EventosgetById($id)
    {
        // Detalle individual: sí carga recursos y descripcion completa.
        return Eventos::with(['recursos:id,nombre,tipo,ubicacion,estado'])
            ->find($id);
    }

    public function Eventoscreate($data)
    {
        $evento = Eventos::create($data);
        $this->flushListCache();
        return $evento;
    }

    public function Eventosupdate($id, $data)
    {
        $affected = Eventos::where('id', $id)->update($data);
        if (! $affected) return null;
        $this->flushListCache();
        return Eventos::find($id);
    }

    public function Eventosdelete($id)
    {
        $deleted = (bool) Eventos::destroy($id);
        if ($deleted) $this->flushListCache();
        return $deleted;
    }

    public function EventosgetByUser($userId)
    {
        return Cache::remember("eventos_user_{$userId}", 60, function () use ($userId) {
            return Eventos::select(self::LIST_COLUMNS)
                ->where('creado_por', $userId)
                ->orderBy('fecha_inicio')
                ->get();
        });
    }

    public function EventosgetByTipo($tipo)
    {
        $key = 'eventos_tipo_' . str_replace(' ', '_', strtolower($tipo));
        return Cache::remember($key, 60, function () use ($tipo) {
            return Eventos::select(self::LIST_COLUMNS)
                ->where('tipo_evento', $tipo)
                ->orderBy('fecha_inicio')
                ->get();
        });
    }

    /**
     * Invalida todas las entradas de caché del listado de eventos.
     * Se llama en create, update y delete para mantener consistencia.
     */
    private function getListCachePrefix(): string
    {
        return 'eventos_list_v' . Cache::rememberForever('eventos_list_version', fn () => 1) . '_';
    }

    private function flushListCache(): void
    {
        $version = Cache::rememberForever('eventos_list_version', fn () => 1);
        Cache::forever('eventos_list_version', $version + 1);
    }
}
