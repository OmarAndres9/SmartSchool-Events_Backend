<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsuariosResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'email'             => $this->email,
            'documento'         => $this->documento,
            'tipo_documento'    => $this->tipo_documento,
            'email_verified_at' => $this->email_verified_at,
            // FIX: incluir roles de Spatie para que el frontend pueda mostrarlos
            'roles'             => $this->getRoleNames(),
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
        ];
    }
}
