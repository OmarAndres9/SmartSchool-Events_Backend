<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Eventos extends Model
{
    use SoftDeletes;
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
        'es_recurrente',
        'tipo_recurrencia',
        'intervalo',
        'dias_semana',
        'fecha_fin_recurrencia',
        'evento_origen_id',
        'visibilidad',
    ];

    protected function casts(): array
    {
        return [
            'es_recurrente'      => 'boolean',
            'intervalo'          => 'integer',
            'dias_semana'        => 'array',
            'fecha_fin_recurrencia' => 'date',
        ];
    }

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

    public function inscripciones()
    {
        return $this->belongsToMany(User::class, 'evento_inscripciones', 'evento_id', 'user_id')
            ->withPivot('estado')
            ->withTimestamps();
    }

    public function inscritosCount()
    {
        return $this->inscripciones()->count();
    }

    public function archivos()
    {
        return $this->hasMany(EventoArchivo::class, 'evento_id');
    }

    public function eventoOrigen()
    {
        return $this->belongsTo(Eventos::class, 'evento_origen_id');
    }

    public function instancias()
    {
        return $this->hasMany(Eventos::class, 'evento_origen_id');
    }

    public function favoritos()
    {
        return $this->belongsToMany(User::class, 'evento_favoritos', 'evento_id', 'user_id')
            ->withTimestamps();
    }

    public function valoraciones()
    {
        return $this->hasMany(EventoValoracion::class, 'evento_id');
    }

    public function ratingPromedio()
    {
        return $this->valoraciones()->avg('puntuacion');
    }
}

