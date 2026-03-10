<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model {
    // CAMBIO VITAL: El nombre real de tu tabla es 'usuarios'
    protected $table = 'usuarios'; 
    
    protected $primaryKey = 'CI';
    public $incrementing = false;
    public $timestamps = false;
    
    // Columnas detectadas en tu base de datos
    protected $fillable = [
        'CI', 'nombre', 'correo', 'contrasena', 
        'telefono', 'idRol', 'estado', 'puntos'
    ];
}