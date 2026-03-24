<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

// CORRECCIÓN: debe extender Pivot (no Model) porque se usa con ->using() en belongsToMany
class EventoRecurso extends Pivot
{
    protected $table = '_evento_recurso_';

    // CORRECCIÓN: Pivot necesita indicar que sí usa timestamps
    public $timestamps = true;

    protected $fillable = [
        'evento_id',
        'recurso_id',
        'cantidad',
    ];

    public function evento()
    {
        // CORRECCIÓN: la clase es Eventos (plural), no Evento
        return $this->belongsTo(Eventos::class, 'evento_id');
    }

    public function recurso()
    {
        return $this->belongsTo(Recursos::class, 'recurso_id');
    }
}
