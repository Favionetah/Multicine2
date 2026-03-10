<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HistorialController extends Controller
{
    public function getHistorial($ci)
    {
        try {
            // Cambiamos 'ventas' por 'compras' según tu base de datos
            $tickets = DB::table('compras') 
                ->join('funciones', 'compras.idFuncion', '=', 'funciones.idFuncion')
                ->join('peliculas', 'funciones.idPelicula', '=', 'peliculas.idPelicula')
                ->join('salas', 'funciones.idSala', '=', 'salas.idSala')
                ->select(
                    'peliculas.titulo',
                    'peliculas.imagenPoster as imagenPoster',
                    'salas.numero as sala',
                    'compras.asientos', // Obtenido de tu tabla 'compras'
                    'funciones.fechaFuncion',
                    'funciones.horaInicio',
                    'compras.total', // Obtenido de tu tabla 'compras'
                    'compras.codigo_ticket' // Obtenido de tu tabla 'compras'
                )
                // Usamos el nombre exacto de la columna para el CI
                ->where('compras.CI_cliente', $ci) 
                ->orderBy('compras.fecha_compra', 'desc')
                ->get();

            // Formateamos la imagen para que cargue correctamente en la vista
            foreach ($tickets as $t) {
                $t->imagenPoster = asset('img/' . $t->imagenPoster);
            }

            return response()->json($tickets);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}