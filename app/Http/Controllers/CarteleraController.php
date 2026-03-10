<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CarteleraController extends Controller
{
    public function getCartelera()
    {
        $funciones = DB::table('funciones')
            ->join('peliculas', 'funciones.idPelicula', '=', 'peliculas.idPelicula')
            ->join('salas', 'funciones.idSala', '=', 'salas.idSala')
            ->select(
                'funciones.idFuncion as id',
                'funciones.fechaFuncion as fecha',
                'funciones.horaInicio as hora',
                'peliculas.titulo',
                'peliculas.imagenPoster as imagen',
                'peliculas.sinopsis',     // Campo recuperado
                'peliculas.duracion',     // Campo recuperado
                'peliculas.genero',       // Campo recuperado
                'peliculas.clasificacion',// Campo recuperado
                'salas.numero as sala_numero',
                'salas.tipoPantalla',
                'salas.filas',
                'salas.columnas',
                'funciones.precioBase as precio',
                'funciones.asientos_vendidos',
                'salas.capacidadTotal'
            )
            ->where('peliculas.estado', 'activa')
            ->get();

        $cartelera = [];
        foreach ($funciones as $f) {
            $cartelera[] = [
                'id' => $f->id,
                'fecha' => $f->fecha,
                'hora' => $f->hora,
                'titulo' => $f->titulo,
                'imagen' => asset('img/' . $f->imagen),
                'sinopsis' => $f->sinopsis,
                'duracion' => $f->duracion,
                'genero' => $f->genero,
                'clasificacion' => $f->clasificacion,
                'sala' => 'SALA ' . $f->sala_numero . ' (' . $f->tipoPantalla . ')',
                'filas' => $f->filas,
                'columnas' => $f->columnas,
                'precio' => (float)$f->precio, // Convertimos a número para evitar error toFixed
                'asientos_vendidos' => $f->asientos_vendidos ?? ''
            ];
        }
        return response()->json($cartelera);
    }
}