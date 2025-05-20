<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contador extends Model
{
    public $timestamps = false;
    protected $table = 'contador';
    protected $fillable = ['fecha_registro', 'contador', 'observacion']; 
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
