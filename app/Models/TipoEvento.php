<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoEvento extends Model
{
    protected $table = 'cultivo_producto';
    protected $fillable = ['descripcion']; 
        protected $hidden = [
        'fecha_registro',
        'estado',
    ];
}
