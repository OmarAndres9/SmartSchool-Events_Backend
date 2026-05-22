<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reporte extends Model
{
    use SoftDeletes;
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
        // CORRECCIÓN: Evento::class no existe, debe ser Eventos::class
        return $this->belongsTo(Eventos::class, 'id_evento');
    }
}
