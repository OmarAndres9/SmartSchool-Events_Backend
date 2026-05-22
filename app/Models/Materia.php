<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'docente_id'];

    public function docente()
    {
        return $this->belongsTo(User::class, 'docente_id');
    }

    public function notas()
    {
        return $this->hasMany(Nota::class, 'materia_id');
    }
}
