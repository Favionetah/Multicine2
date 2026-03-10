<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class Usuario extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'CI';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'CI', 'nombre', 'correo', 'contrasena', 'telefono', 'idRol', 'estado', 'puntos'
    ];

    protected $hidden = [
        'contrasena',
    ];

    public $timestamps = false;

    /**
     * Indica a Laravel cuál es el nombre del campo de contraseña.
     * Necesario porque el campo no se llama 'password' (default de Laravel).
     */
    public function getAuthPasswordName(): string
    {
        return 'contrasena';
    }

    /**
     * Métodos requeridos por la interfaz JWTSubject
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }
}