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
            // FIX: estos campos existían en la BD pero no se retornaban
            'lugar'           => $this->lugar,
            'tipo_evento'     => $this->tipo_evento,
            'modalidad'       => $this->modalidad,
            'grupo_destinado' => $this->grupo_destinado,
            'creado_por'      => $this->creado_por,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
        ];
    }
}
