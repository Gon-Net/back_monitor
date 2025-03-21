<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoObservador extends Model
{
    protected $table = 'tipo_observador';

    protected $fillable = ['descripcion']; 
    protected $hidden = [
        'fecha_registro',
        'estado',
    ];
}
