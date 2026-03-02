<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recursos extends Model
{
    protected $table = '_recursos__table';
    protected $fillable = [
        'nombre',
        'ubicacion',
        'estado',
    ];
}
