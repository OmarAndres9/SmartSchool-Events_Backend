<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $table = '_roles_';
    protected $fillable = [
        'name',
        'description',
    ];

}
