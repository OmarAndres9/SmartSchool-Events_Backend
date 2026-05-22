<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventoArchivo extends Model
{
    protected $table = 'evento_archivos';

    protected $fillable = [
        'evento_id',
        'nombre_original',
        'ruta',
        'tipo',
        'tamano',
    ];

    public function evento()
    {
        return $this->belongsTo(Eventos::class, 'evento_id');
    }
}
