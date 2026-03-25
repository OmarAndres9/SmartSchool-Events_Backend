<?php

namespace App\Repository\Eloquent;

use App\Models\Eventos;
use App\Repository\Interfaces\EventosInterfaces;

class EventosRepository implements EventosInterfaces
{
    /**
     * Columnas base que se devuelven en listados.
     * Excluimos "descripcion" (TEXT largo) para que el listado sea rápido;
     * el detalle individual sí la devuelve completa.
     */
    private const LIST_COLUMNS = [
        'id', 'nombre', 'fecha_inicio', 'fecha_fin',
        'lugar', 'tipo_evento', 'modalidad', 'grupo_destinado', 'creado_por',
        'created_at', 'updated_at',
    ];

    public function EventosgetAll($perPage = null)
    {
        $query = Eventos::with(['recursos:id,nombre,tipo,ubicacion,estado'])
            ->select(self::LIST_COLUMNS);

        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    public function EventosgetById($id)
    {
        // Detalle: sí incluye descripcion y recursos completos
        return Eventos::with(['recursos:id,nombre,tipo,ubicacion,estado'])
            ->find($id);
    }

    public function Eventoscreate($data)
    {
        return Eventos::create($data);
    }

    public function Eventosupdate($id, $data)
    {
        // updateOrFail + firstOrFail en una sola query usando update directo
        $affected = Eventos::where('id', $id)->update($data);
        if (! $affected) return null;
        return Eventos::find($id);
    }

    public function Eventosdelete($id)
    {
        return (bool) Eventos::destroy($id);
    }

    public function EventosgetByUser($userId)
    {
        return Eventos::with(['recursos:id,nombre,tipo,ubicacion,estado'])
            ->select(self::LIST_COLUMNS)
            ->where('creado_por', $userId)
            ->orderBy('fecha_inicio')
            ->get();
    }

    public function EventosgetByTipo($tipo)
    {
        return Eventos::with(['recursos:id,nombre,tipo,ubicacion,estado'])
            ->select(self::LIST_COLUMNS)
            ->where('tipo_evento', $tipo)
            ->orderBy('fecha_inicio')
            ->get();
    }
}

