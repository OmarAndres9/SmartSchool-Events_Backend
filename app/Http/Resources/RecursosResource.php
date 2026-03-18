<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RecursosResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'nombre'    => $this->nombre,
            'ubicacion' => $this->ubicacion,
            'estado'    => $this->estado,
            // FIX: incluir eventos asignados para mostrarlos en Avisos
            'eventos'   => $this->whenLoaded('eventos', function () {
                return $this->eventos->map(fn($ev) => [
                    'id'     => $ev->id,
                    'nombre' => $ev->nombre,
                    'pivot'  => ['cantidad' => $ev->pivot->cantidad ?? 1],
                ]);
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
