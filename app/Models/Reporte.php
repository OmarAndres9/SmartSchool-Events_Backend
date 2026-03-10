<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    protected $table = '_reportes_';

    protected $fillable = [
        'tipo',
        'descripcion',
        'fecha',
        'estado',
        'id_usuario',
        'id_evento',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'id_evento');
    }
}
