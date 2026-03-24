<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RecursosResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'nombre'      => $this->nombre,
            // CORRECCIÓN: estos campos existían en BD pero no se retornaban
            'tipo'        => $this->tipo,
            'ubicacion'   => $this->ubicacion,
            'capacidad'   => $this->capacidad,
            'estado'      => $this->estado,
            'descripcion' => $this->descripcion,
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
