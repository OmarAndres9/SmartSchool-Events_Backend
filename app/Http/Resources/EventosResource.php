<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventosResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'nombre'          => $this->nombre,
            'descripcion'     => $this->descripcion,
            'fecha_inicio'    => $this->fecha_inicio,
            'fecha_fin'       => $this->fecha_fin,
            'lugar'           => $this->lugar,
            'tipo_evento'     => $this->tipo_evento,
            'modalidad'       => $this->modalidad,
            'grupo_destinado' => $this->grupo_destinado,
            'creado_por'      => $this->creado_por,
            // CORRECCIÓN: exponer recursos del evento cuando se carguen
            'recursos'        => $this->whenLoaded('recursos', function () {
                return $this->recursos->map(fn($rec) => [
                    'id'       => $rec->id,
                    'nombre'   => $rec->nombre,
                    'tipo'     => $rec->tipo,
                    'ubicacion'=> $rec->ubicacion,
                    'estado'   => $rec->estado,
                    'pivot'    => ['cantidad' => $rec->pivot->cantidad ?? 1],
                ]);
            }),
            'inscritos_count' => $this->inscripciones_count ?? 0,
            'rating_promedio' => $this->when($this->relationLoaded('valoraciones'), fn() => round($this->valoraciones->avg('puntuacion'), 1)),
            'rating_count'    => $this->when($this->relationLoaded('valoraciones'), fn() => $this->valoraciones->count()),
            'favoritos_count' => $this->favoritos_count ?? 0,
            'visibilidad'     => $this->visibilidad ?? 'publico',
            'es_recurrente'   => $this->es_recurrente,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
        ];
    }
}
