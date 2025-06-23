<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
class ObservadorAuth extends Authenticatable
{
    use HasApiTokens;
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $table = 'observador';
    protected $fillable = [
        'nombre_usuario', 
        'numero_documento_identidad'
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
    public function getAuthIdentifierName()
    {
        return 'nombre_usuario';
    }
    public function getAuthPassword()
    {
        return $this->numero_documento_identidad;
    }
    public function validateForPassportPasswordGrant($nombre_usuario, $numero_documento_identidad)
    {
        return $this->where('nombre_usuario', $nombre_usuario)
                    ->where('numero_documento_identidad', $numero_documento_identidad)
                    ->first();
    }
}
