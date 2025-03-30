<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventoExtremo extends Model
{
    public $timestamps = false;
    protected $table = 'evento_extremo';
    protected $fillable = [
        'ubicacion_id', 
        'tipo_evento_id', 
        'tipo_intensidad_evento_id', 
        'numero_veces', 
        'observacion', 
        'observador_id', 
        'fecha_registro_evento',
        'estado_cultivo_papa',
        'estado_cultivo_haba',
        'estado_cultivo_tomate',
        'estado_cultivo_cebolla',
        'estado_cultivo_maiz',
        'estado_cultivo_zanahoria',
        'estado_cultivo_durazno',
        'estado_cultivo_manzana',
        'otro_cultivo'
    ]; 
    

    protected $hidden = [
        'fecha_registro',
        'estado'
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