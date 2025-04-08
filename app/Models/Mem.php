<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mem extends Model
{
    public $timestamps = false;
    protected $table = 'mem_dato_fecha';
    protected $fillable = [
        'id_pem',
        'fecha',
        'hora',
        'temperatura',
        'humedad',
        'presion',
        'uv',
        'precipitacion_tipo',
        'precipitacion_probabilidad',
        'precipitacion'
    ]; 
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