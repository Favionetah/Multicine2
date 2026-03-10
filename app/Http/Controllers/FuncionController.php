<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Funcion; 

class FuncionController extends Controller {

    // Listar Funciones (Uniendo datos de película y sala)
    public function index() {
        $funciones = \App\Models\Funcion::from('funciones')
            ->join('peliculas', 'funciones.idPelicula', '=', 'peliculas.idPelicula')
            ->join('salas', 'funciones.idSala', '=', 'salas.idSala')
            ->select(
                'funciones.*', 
                'peliculas.titulo', 
                'peliculas.imagenPoster', 
                'salas.nombre as sala'
            )
            ->orderBy('funciones.fechaFuncion', 'asc')
            ->orderBy('funciones.horaInicio', 'asc')
            ->get();
            
        return response()->json($funciones);
    }

    // Crear Funciones (Soporta múltiples días)
    public function store(Request $request) {
        $fechaActual = $request->fechaFuncion;
        $fechaFin = $request->filled('fechaFin') ? $request->fechaFin : $fechaActual;
        
        $creadas = 0;

        while (strtotime($fechaActual) <= strtotime($fechaFin)) {
            // Verifica choque de horarios
            $ocupada = Funcion::where('idSala', $request->idSala)
                              ->where('fechaFuncion', $fechaActual)
                              ->where('horaInicio', $request->horaInicio)
                              ->exists();

            if (!$ocupada) {
                $funcion = new Funcion();
                $funcion->idPelicula = $request->idPelicula;
                $funcion->idSala = $request->idSala;
                $funcion->fechaFuncion = $fechaActual;
                $funcion->horaInicio = $request->horaInicio;
                // Sumamos 2 horas estándar de duración
                $funcion->horaFin = date('H:i:s', strtotime($request->horaInicio) + 7200); 
                $funcion->precioBase = $request->precioBase;
                $funcion->save();
                $creadas++;
            }
            $fechaActual = date('Y-m-d', strtotime($fechaActual . ' + 1 day'));
        }

        if ($creadas > 0) {
            $peli = \App\Models\Pelicula::find($request->idPelicula);
            $titulo = $peli ? $peli->titulo : "Pelicula ID: {$request->idPelicula}";
            
            \App\Models\Log::create([
                'fecha' => now(),
                'nombre' => session('nombre', 'Admin'),
                'rol' => session('rol', 'administrador'),
                'accion' => "Programó {$creadas} nuevas funciones para la película: {$titulo}"
            ]);

            return response()->json(['status' => 'success', 'message' => "Se guardaron $creadas funciones."]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'La sala ya estaba ocupada en ese horario.']);
        }
    }

    // Editar Función individual
    public function editar(Request $request) {
        // Evitar choques con otras películas al cambiar el horario
        $ocupada = Funcion::where('idSala', $request->idSala)
                          ->where('fechaFuncion', $request->fechaFuncion)
                          ->where('horaInicio', $request->horaInicio)
                          ->where('idFuncion', '!=', $request->idFuncion)
                          ->exists();

        if ($ocupada) {
            return response()->json(['status' => 'error', 'message' => 'La sala ya está ocupada en ese horario.']);
        }

        $funcion = Funcion::findOrFail($request->idFuncion);
        $funcion->idPelicula = $request->idPelicula;
        $funcion->idSala = $request->idSala;
        $funcion->fechaFuncion = $request->fechaFuncion;
        $funcion->horaInicio = $request->horaInicio;
        $funcion->horaFin = date('H:i:s', strtotime($request->horaInicio) + 7200);
        $funcion->precioBase = $request->precioBase;
        $funcion->save();

        $peli = \App\Models\Pelicula::find($funcion->idPelicula);
        $titulo = $peli ? $peli->titulo : "Pelicula ID: {$funcion->idPelicula}";

        \App\Models\Log::create([
            'fecha' => now(),
            'nombre' => session('nombre', 'Admin'),
            'rol' => session('rol', 'administrador'),
            'accion' => "Editó horario de función: {$titulo} (ID: {$request->idFuncion}) para el {$request->fechaFuncion} a las {$request->horaInicio}"
        ]);

        return response()->json(['status' => 'success', 'message' => 'Función actualizada.']);
    }

    // Eliminar Función
    public function eliminar(Request $request) {
        try {
            $funcion = Funcion::where('idFuncion', $request->id)->first();
            
            if ($funcion) {
                $peli = \App\Models\Pelicula::find($funcion->idPelicula);
                $titulo = $peli ? $peli->titulo : "Pelicula ID: {$funcion->idPelicula}";
                $fecha = $funcion->fechaFuncion;
                $hora = $funcion->horaInicio;

                $funcion->delete();

                \App\Models\Log::create([
                    'fecha' => now(),
                    'nombre' => session('nombre', 'Admin'),
                    'rol' => session('rol', 'administrador'),
                    'accion' => "Eliminó la función de '{$titulo}' programada para el {$fecha} {$hora}"
                ]);

                return response()->json(['status' => 'success', 'message' => 'Función eliminada.']);
            }
            
            return response()->json(['status' => 'error', 'message' => 'No se encontró la función.'], 404);

        } catch (\Exception $e) {
            // Prevenir eliminación si ya existen boletos (Foreign Key Error)
            return response()->json([
                'status' => 'error', 
                'message' => 'No se puede eliminar: Esta función ya tiene ventas registradas.'
            ], 500);
        }
    }
}