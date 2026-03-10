<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sala extends Model {
    protected $table = 'salas';
    protected $primaryKey = 'idSala';
    public $timestamps = false;
    
    // Agregamos todas las columnas que vimos en tu phpMyAdmin
    protected $fillable = [
        'nombre', 'filas', 'columnas', 'capacidadTotal', 
        'tipo', 'precio', 'numero', 'tipoPantalla', 'estado', 'capacidad', 'imagen'
    ];
}