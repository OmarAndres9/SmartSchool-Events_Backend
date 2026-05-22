<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notificaciones extends Model
{
    use SoftDeletes;
    protected $table = 'notificaciones';

    protected $fillable = [
        'titulo',
        'mensaje',
        'tipo',
        'canal',
        'fecha_creacion',
        'id_usuario',
        'id_evento',
    ];
}
