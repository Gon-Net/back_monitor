<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoObservadorCategoria extends Model
{
    protected $table = 'tipo_observador_categoria';

    protected $fillable = ['descripcion']; 
    protected $hidden = [
        'fecha_registro',
        'estado',
    ];
}
