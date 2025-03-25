<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoFrecuencia extends Model
{
    protected $table = 'tipo_frecuencia';

    protected $fillable = ['descripcion']; 
    protected $hidden = [
        'fecha_registro',
        'estado',
    ];
}


