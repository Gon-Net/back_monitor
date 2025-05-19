<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prediccion extends Model
{
    public $timestamps = false;
    protected $table = 'pronosticos';
    protected $fillable = [
        'fecha_registro',
        'hora',
        'dia',
        'velocidad_viento',
        'direccion_viento',
        'temperatura',
        'humedad',
        'probabilidad_lluvia',
        'detalle',
        'indice_uv',
        'descripcion',
        'minimo',
        'detalle'
    ];
    protected $hidden = [
        'estado'
    ];
    protected $attributes = [
        'estado' => 'A',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->fecha_registro) {
                $model->fecha_registro = now();
            }
        });
    }
}


