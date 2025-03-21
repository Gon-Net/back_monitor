<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Precipitacion extends Model
{
    public $timestamps = false;
    protected $table = 'precipitacion';
    protected $fillable = ['ubicacion_id', 'tipo_frecuencia_id', 'intervalo', 'valor', 'observador_id']; 
    protected $hidden = [
        'fecha_registro',
        'fecha_modificacion',
        'fecha_eliminacion',
        'estado',
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

        static::updating(function ($model) {
            if (!$model->fecha_modificacion) {
                $model->fecha_modificacion = now();
            }
        });
    }
}


