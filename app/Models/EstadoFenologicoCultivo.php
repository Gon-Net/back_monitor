<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoFenologicoCultivo extends Model
{
    protected $table = 'estado_fenologico_cultivo';
    protected $fillable = [
        'evento_extremo_id', 
        'cultivo_producto_id', 
        'germinacion', 
        'desarrollo', 
        'floracion', 
        'fructificacion',
        'observador_id' 
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

    public function eventoExtremo()
    {
        return $this->belongsTo(EventoExtremo::class, 'evento_extremo_id');
    }

    public function cultivoProducto()
    {
        return $this->belongsTo(CultivoProducto::class, 'cultivo_producto_id');
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