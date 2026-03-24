<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Eventos extends Model
{
    protected $table = 'eventos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'lugar',
        'tipo_evento',
        'modalidad',
        'grupo_destinado',
        'creado_por',

    ];

    public function recursos()
    {
        // CORRECCIÓN: era Recurso::class (no existe), debe ser Recursos::class
        return $this->belongsToMany(Recursos::class, '_evento_recurso_', 'evento_id', 'recurso_id')
            ->using(EventoRecurso::class)
            ->withPivot('cantidad')
            ->withTimestamps();
    }
}
