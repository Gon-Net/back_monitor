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
        'microestacion'
    ];

    protected $attributes = [
        'estado' => 'A',
    ];

    protected $appends = ['lugar_pem', 'latitud_pem', 'longitud_pem'];

    public function microestacion()
    {
        return $this->belongsTo(MicroEstacion::class, 'id_pem', 'id_pem');
    }

    public function getLugarPemAttribute()
    {
        return $this->microestacion->lugar_pem ?? '';
    }
    public function getLatitudPemAttribute()
    {
        return $this->microestacion->latitud ?? null;
    }
    public function getLongitudPemAttribute()
    {
        return $this->microestacion->longitud ?? null;
    }
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

