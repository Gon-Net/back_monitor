<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventoExtremo extends Model
{
    protected $table = 'evento_extremo';
    protected $fillable = [
        'ubicacion_id', 
        'tipo_evento_id', 
        'tipo_intensidad_evento_id', 
        'numero_veces', 
        'observacion', 
        'observador_id', 
    ]; 
    

    protected $hidden = [
        'fecha_registro',
        'estado',
        'fecha_modificacion',
        'fecha_eliminacion'
    ];

    protected $attributes = [
        'estado' => 'A',
    ];

    public function tipo_intensidad_evento()
     {
         return $this->belongsTo(TipoIntensidadEvento::class, 'tipo_intensidad_evento_id');
     }
 
     public function ubicacion()
     {
         return $this->belongsTo(Ubicacion::class, 'ubicacion_id');
     }

     public function tipo_evento()
     {
         return $this->belongsTo(TipoEvento::class, 'tipo_evento_id');
     }

     public function observador()
     {
         return $this->belongsTo(Observador::class, 'observador_id');
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