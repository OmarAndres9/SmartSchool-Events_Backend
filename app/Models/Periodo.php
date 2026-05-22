<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    protected $fillable = ['nombre', 'fecha_inicio', 'fecha_fin', 'activo'];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin'    => 'date',
            'activo'       => 'boolean',
        ];
    }

    public function notas()
    {
        return $this->hasMany(Nota::class, 'periodo_id');
    }
}
