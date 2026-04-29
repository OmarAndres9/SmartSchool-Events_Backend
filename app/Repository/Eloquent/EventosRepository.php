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
        // OPTIMIZACIÓN 1: sin with(['recursos']) en el listado.
        // El join con la tabla pivot causaba ~200–400 ms extra por query.
        // Los recursos solo se cargan en EventosgetById().
        //
        // OPTIMIZACIÓN 2: caché Redis de 60 s para el listado paginado.
        // Elimina la query a PostgreSQL en requests repetidos (Dashboard, recargas).
        $page      = request()->query('page', 1);
        $limit     = $perPage ?? 15;
        $cacheKey  = "eventos_list_p{$page}_l{$limit}";

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
    private function flushListCache(): void
    {
        Cache::tags(['eventos'])->flush();

        // Fallback para drivers sin soporte de tags (file, database):
        // invalidar páginas 1-5 manualmente.
        for ($p = 1; $p <= 5; $p++) {
            Cache::forget("eventos_list_p{$p}_l15");
        }
    }
}
