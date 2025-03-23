<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Observador extends Model
{
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $table = 'observador';
    protected $fillable = [
        'ubicacion_id', 
        'tipo_observador_id', 
        'tipo_usuarioapk_id', 
        'nombre_observador', 
        'numero_documento_identidad', 
        'fecha_nacimiento', 
        'numero_celular', 
        'correo', 
        'nombre_usuario', 
        'dir_documento_identidad', 
        'dir_acta_nombramiento'
    ]; 
    

    protected $hidden = [
        'fecha_registro',
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
    public function ubicacion()
     {
         return $this->belongsTo(Ubicacion::class, 'ubicacion_id');
     }
 
     public function tipoObservador()
     {
         return $this->belongsTo(TipoObservador::class, 'tipo_observador_id');
     }
 
     public function tipoObservadorCategoria()
     {
         return $this->belongsTo(TipoObservadorCategoria::class, 'tipo_usuarioapk_id');
     }
}
