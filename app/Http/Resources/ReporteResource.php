<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReporteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tipo' => $this->tipo,
            'descripcion' => $this->descripcion,
            'fecha' => $this->fecha,
            'estado' => $this->estado,
            'id_usuario' => $this->id_usuario,
            'id_evento' => $this->id_evento,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
