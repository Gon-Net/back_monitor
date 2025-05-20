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


