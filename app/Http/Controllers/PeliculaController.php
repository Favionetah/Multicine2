<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelicula;

class PeliculaController extends Controller
{
    // Obtener todas las películas (GET)
    public function index() {
        return response()->json(Pelicula::all());
    }

    // Crear o Editar Película (POST)
    public function store(Request $request) {
        // Validación básica
        $request->validate([
            'titulo' => 'required|string|max:255',
            'duracion' => 'required|numeric',
            'genero' => 'required|string',
        ]);

        // Si llega un idPelicula, buscamos para editar; si no, creamos una nueva
        $pelicula = $request->filled('idPelicula') 
                    ? Pelicula::findOrFail($request->idPelicula) 
                    : new Pelicula();

        // Mapeo de datos
        $pelicula->titulo = $request->titulo;
        $pelicula->sinopsis = $request->sinopsis;
        $pelicula->genero = $request->genero;
        $pelicula->duracion = $request->duracion;
        $pelicula->clasificacion = $request->clasificacion;
        $pelicula->idioma = $request->idioma;
        $pelicula->estado = $request->estado ?? 'activa';

        // Gestión de la imagen en public/img
        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('img'), $filename);
            $pelicula->imagenPoster = $filename;
        }

        $pelicula->save();

        \App\Models\Log::create([
            'fecha' => now(),
            'nombre' => session('nombre', 'Admin'),
            'rol' => session('rol', 'administrador'),
            'accion' => ($request->filled('idPelicula') ? 'Actualizó' : 'Creó') . " la película: {$pelicula->titulo}"
        ]);
        
        return response()->json([
            "status" => "success", 
            "message" => "Película guardada correctamente.",
            "pelicula" => $pelicula
        ]);
    }

    // Eliminar Película (POST)
    public function eliminar(Request $request) {
        $pelicula = Pelicula::find($request->id); 
        if ($pelicula) {
            $titulo = $pelicula->titulo;
            $pelicula->delete();

            \App\Models\Log::create([
                'fecha' => now(),
                'nombre' => session('nombre', 'Admin'),
                'rol' => session('rol', 'administrador'),
                'accion' => "Eliminó la película: {$titulo}"
            ]);

            return response()->json(["status" => "success"]);
        }
        return response()->json(["status" => "error", "message" => "No se encontró la película."]);
    }
}