<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recursos extends Model
{
    protected $table = '_recursos__table';

    protected $fillable = [
        'nombre',
        'tipo',
        'ubicacion',
        'capacidad',
        'estado',
        'descripcion',
    ];

    public function eventos()
    {
        return $this->belongsToMany(Eventos::class, '_evento_recurso_', 'recurso_id', 'evento_id')
            ->withPivot('cantidad')
            ->withTimestamps();
    }
}
