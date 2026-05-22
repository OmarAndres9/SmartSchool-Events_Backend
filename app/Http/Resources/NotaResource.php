<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'calificacion' => (float) $this->calificacion,
            'materia'      => [
                'id'     => $this->materia->id,
                'nombre' => $this->materia->nombre,
            ],
            'periodo' => [
                'id'     => $this->periodo->id,
                'nombre' => $this->periodo->nombre,
            ],
            'registrado_por' => $this->registradoPor?->name,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];
    }
}
