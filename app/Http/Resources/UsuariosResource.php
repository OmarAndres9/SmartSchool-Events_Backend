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
            // FIX: devolver roles como array de objetos {id, name}
            // para que el frontend acceda a user.roles[0].name correctamente
            'roles'             => $this->roles->map(fn($r) => [
                'id'   => $r->id,
                'name' => $r->name,
            ]),
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
        ];
    }
}
