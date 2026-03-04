<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventoRecurso extends Model
{
    protected $table = '_evento_recurso_';

    protected $fillable = [
        'evento_id',
        'recurso_id',
        'cantidad',
    ];

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }
}
