<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificacionesResource extends JsonResource
{
    
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'mensaje' => $this->mensaje,
            'tipo' => $this->tipo,
            'canal' => $this->canal,
            'fecha_creacion' => $this->fecha_creacion,
            'id_usuario' => $this->id_usuario,
            'id_evento' => $this->id_evento,
        ];
    }
}
