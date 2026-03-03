<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuarios extends Model
{
    protected $table = '_usuarios__table';
    protected $fillable = [
        'nombre',
        'email',
        'password',
        'rol_id',
        'documento',
        'tipo_documento',
        
    ];

    public function rol()
    {
        return $this->belongsTo(Roles::class, 'rol_id');
    }
}
