<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mem extends Model
{
    protected $table = 'mem_dato_fecha';

    protected $hidden = [
        'fecha_registro',
        'fecha_modificacion',
        'fecha_eliminacion',
        'estado',
    ];
}