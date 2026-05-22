<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventoValoracion extends Model
{
    protected $table = 'evento_valoraciones';

    protected $fillable = [
        'evento_id',
        'user_id',
        'puntuacion',
        'comentario',
    ];

    public function evento()
    {
        return $this->belongsTo(Eventos::class, 'evento_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
