<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ubicacion extends Model
{
    protected $table = 'ubicacion';

    protected $fillable = ['departamento_id', 'departamento', 'provincia', 'municipio', 'comunidad']; 
    protected $hidden = [
        'fecha_registro',
        'estado',
    ];
}


