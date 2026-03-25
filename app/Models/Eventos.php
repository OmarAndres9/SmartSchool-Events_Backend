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

    /**
     * OPTIMIZACIÓN: NO usar $with = ['recursos'] a nivel de modelo.
     * Cargar recursos siempre (global eager) genera queries innecesarias
     * en listados paginados. Cada repositorio/controlador los carga
     * explícitamente solo cuando los necesita.
     */

    public function recursos()
    {
        return $this->belongsToMany(Recursos::class, '_evento_recurso_', 'evento_id', 'recurso_id')
            ->using(EventoRecurso::class)
            ->withPivot('cantidad')
            ->withTimestamps();
    }
}

