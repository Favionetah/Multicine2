<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Funcion;

class ReporteController extends Controller
{
    public function generar(Request $request)
    {
        $request->validate([
            'fechaInicio' => 'required|date',
            'fechaFin' => 'required|date',
        ]);

        // 1. Obtenemos todas las funciones en el rango de fechas
        $funciones = \App\Models\Funcion::from('funciones')
            ->join('peliculas', 'funciones.idPelicula', '=', 'peliculas.idPelicula')
            ->join('salas', 'funciones.idSala', '=', 'salas.idSala')
            ->whereBetween('funciones.fechaFuncion', [$request->fechaInicio, $request->fechaFin])
            ->select(
                'funciones.idFuncion',
                'funciones.fechaFuncion',
                'funciones.horaInicio',
                'funciones.precioBase',
                'peliculas.titulo as pelicula',
                'salas.nombre as sala',
                'salas.capacidadTotal as capacidad'
            )
            ->get();

        // 2. Para cada función, buscamos sus compras reales para calcular totales precisos
        $datos = $funciones->map(function ($f) {
            // Buscamos todas las compras asociadas a esta función
            $compras = \Illuminate\Support\Facades\DB::table('compras')
                ->where('idFuncion', $f->idFuncion)
                ->get();

            $totalRecaudado = 0;
            $totalBoletos = 0;

            foreach ($compras as $compra) {
                // Sumamos el dinero real pagado
                $totalRecaudado += (float)$compra->total;

                // Contamos los boletos (asientos) en esta compra
                // El campo 'asientos' suele ser "A1, A2" o ["A1", "A2"]
                $asientosRaw = $compra->asientos;
                if (!empty($asientosRaw)) {
                    // Si es una cadena separada por comas
                    $listaAsientos = explode(',', $asientosRaw);
                    // Quitamos espacios y filtramos vacíos
                    $listaAsientos = array_filter(array_map('trim', $listaAsientos));
                    $totalBoletos += count($listaAsientos);
                }
            }

            // Asignamos los valores calculados al objeto
            $f->recaudacion = number_format($totalRecaudado, 2, '.', '');
            $f->boletos_vendidos = $totalBoletos;
            
            // Ocupación calculada al momento
            $f->porcentaje_ocupacion = $f->capacidad > 0 
                ? round(($totalBoletos / $f->capacidad) * 100) 
                : 0;

            // Aseguramos tipos para el JSON
            $f->capacidad = (int)$f->capacidad;
            $f->idFuncion = (int)$f->idFuncion;

            return $f;
        });

        // Ordenamos por fecha y hora
        $datos = $datos->sortBy(['fechaFuncion', 'horaInicio'])->values();

        return response()->json([
            'status' => 'success',
            'count' => $datos->count(),
            'total_recaudado_periodo' => $datos->sum(function($item){ return (float)$item->recaudacion; }),
            'total_boletos_periodo' => $datos->sum('boletos_vendidos'),
            'data' => $datos
        ]);
    }
}