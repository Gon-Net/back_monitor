<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoIntensidadEvento extends Model
{
    protected $table = 'tipo_intensidad_evento';
    protected $fillable = ['descripcion']; 
        protected $hidden = [
        'fecha_registro',
        'estado',
    ];
}
