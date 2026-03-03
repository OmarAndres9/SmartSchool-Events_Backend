<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recursos extends Model
{
    protected $table = '_Recursos__table';
    protected $fillable = [
        'nombre',
        'ubicacion',
        'estado',
    ];
}
