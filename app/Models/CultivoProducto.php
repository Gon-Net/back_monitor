<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CultivoProducto extends Model
{
    protected $table = 'contador';
    protected $fillable = ['descripcion']; 
    protected $hidden = [
        'fecha_registro',
        'estado',
    ];
}


