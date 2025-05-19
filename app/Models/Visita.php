<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    public $timestamps = false;
    protected $table = 'visitas';

    protected $fillable = ['fecha_registro', 'usuario', 'ubicacion_id', 'observacion']; 
    protected $hidden = [
        'estado',
    ];
    protected $attributes = [
        'estado' => 'A',
    ];
}


