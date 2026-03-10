<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelicula extends Model
{
    protected $table = 'peliculas';
    protected $primaryKey = 'idPelicula';
    public $timestamps = false;

    protected $fillable = [
        'titulo', 'sinopsis', 'duracion', 'genero', 
        'clasificacion', 'idioma', 'imagenPoster', 'estado' // <--- Agregamos 'estado'
    ];
}