<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MicroEstacion extends Model
{
    protected $table = 'microestacion';
 
    protected $hidden = [
        'fecha_registro',
        'fecha_modificacion',
        'fecha_eliminacion',
        'estado',
    ];
}


