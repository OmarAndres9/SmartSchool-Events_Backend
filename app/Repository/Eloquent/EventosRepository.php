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
        $search    = request()->query('search');
        $sortBy    = request()->query('sort', 'created_at');
        $sortDir   = request()->query('order', 'desc');
        $fechaDesde = request()->query('fecha_desde');
        $fechaHasta = request()->query('fecha_hasta');

        $allowedSort = ['created_at', 'nombre', 'fecha_inicio', 'fecha_fin', 'tipo_evento', 'lugar'];
        if (! in_array($sortBy, $allowedSort)) $sortBy = 'created_at';
        if (! in_array(strtolower($sortDir), ['asc', 'desc'])) $sortDir = 'desc';

        $prefix   = $this->getListCachePrefix();
        $cacheKey = "{$prefix}list_p{$page}_l{$limit}_s{$search}_o{$sortBy}_{$sortDir}_fd{$fechaDesde}_fh{$fechaHasta}";

        return Cache::remember($cacheKey, 60, function () use ($limit, $search, $sortBy, $sortDir, $fechaDesde, $fechaHasta) {
            $query = Eventos::select(self::LIST_COLUMNS)
                ->with(['recursos:id,nombre,tipo,ubicacion,estado'])
                ->withCount('inscripciones');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nombre', 'ilike', "%{$search}%")
                      ->orWhere('lugar', 'ilike', "%{$search}%")
                      ->orWhere('descripcion', 'ilike', "%{$search}%");
                });
            }

            if ($fechaDesde) {
                $query->where('fecha_inicio', '>=', $fechaDesde);
            }

            if ($fechaHasta) {
                $query->where('fecha_fin', '<=', $fechaHasta);
            }

            return $query->orderBy($sortBy, $sortDir)->paginate($limit);
        });
    }

    public function EventosgetById($id)
    {
        // Detalle individual: sí carga recursos y descripcion completa.
        return Eventos::with(['recursos:id,nombre,tipo,ubicacion,estado'])
            ->withCount('inscripciones')
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
                ->with(['recursos:id,nombre,tipo,ubicacion,estado'])
                ->withCount('inscripciones')
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
                ->with(['recursos:id,nombre,tipo,ubicacion,estado'])
                ->withCount('inscripciones')
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
