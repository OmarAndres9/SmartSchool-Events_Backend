<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    protected $fillable = [
        'solicitante_id', 'destinatario_id',
        'fecha_solicitada', 'motivo', 'comentario', 'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_solicitada' => 'datetime',
        ];
    }

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'solicitante_id');
    }

    public function destinatario()
    {
        return $this->belongsTo(User::class, 'destinatario_id');
    }
}
