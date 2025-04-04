<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoEvento extends Model
{
    protected $table = 'tipo_evento';
    protected $fillable = ['descripcion']; 
        protected $hidden = [
        'fecha_registro',
        'estado',
    ];
}
