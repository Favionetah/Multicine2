<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Funcion extends Model
{
    protected $table = 'funciones';
    
    // ¡ESTA LÍNEA ES LA QUE SOLUCIONA EL ERROR 500!
    protected $primaryKey = 'idFuncion'; 
    
    public $timestamps = false;

    protected $fillable = [
        'idPelicula', 'idSala', 'fechaFuncion', 
        'horaInicio', 'horaFin', 'precioBase',
        'boletos_vendidos', 'asientos_vendidos'
    ];
}