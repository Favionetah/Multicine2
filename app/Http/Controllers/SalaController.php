<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sala;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SalaController extends Controller
{
    /**
     * Listar todas las salas.
     * Siempre retorna JSON (usado por ambos flujos).
     */
    public function index()
    {
        return response()->json(Sala::all());
    }

    /**
     * Ver una sala individual por ID.
     * Siempre retorna JSON (usado por ambos flujos).
     */
    public function show($id)
    {
        $sala = Sala::find($id);

        if (!$sala) {
            return response()->json(['error' => 'Sala no encontrada'], 404);
        }

        return response()->json($sala);
    }

    /**
     * Crear o editar una sala (uso general - POST /api/salas con campo 'id').
     */
    public function store(Request $request)
    {
        return $this->procesarGuardado($request, $request->id);
    }

    /**
     * Actualizar una sala (estándar REST - PUT /api/salas/{id}).
     */
    public function update(Request $request, $id)
    {
        return $this->procesarGuardado($request, $id);
    }

    /**
     * Lógica unificada para crear o actualizar una sala.
     */
    protected function procesarGuardado(Request $request, $id = null)
    {
        Log::info('Datos recibidos para Sala:', ['id' => $id, 'data' => $request->all()]);

        // Si hay ID, buscamos para editar; si no, creamos nueva.
        $sala = $id ? Sala::find($id) : new Sala();

        if ($id && !$sala) {
            $mensaje = ['status' => 'error', 'message' => 'Sala no encontrada.'];
            return $request->expectsJson()
                ? response()->json($mensaje, 404)
                : redirect()->back()->with('error', $mensaje['message']);
        }

        // Mapeo de campos
        $sala->nombre        = $request->nombre;
        $sala->filas         = $request->filas;
        $sala->columnas      = $request->columnas;

        // Cálculo automático de capacidad
        $total               = $request->filas * $request->columnas;
        $sala->capacidadTotal = $total;
        $sala->capacidad      = $total;

        $sala->tipo          = $request->tipo;
        $sala->precio        = $request->precio;
        $sala->numero        = $request->numero ?? 0;
        $sala->tipoPantalla  = ($request->tipo == 'xl') ? 'MAX' : '2D';
        $sala->estado        = 'activa';

        $sala->save();

        \App\Models\Log::create([
            'fecha' => now(),
            'nombre' => session('nombre', 'Admin'),
            'rol' => session('rol', 'administrador'),
            'accion' => ($id ? 'Actualizó' : 'Creó') . " la sala: {$sala->nombre}"
        ]);

        $respuesta = [
            'status'  => 'success',
            'message' => $id ? 'Sala actualizada correctamente.' : 'Sala creada correctamente.',
            'data'    => $sala,
        ];

        if ($request->expectsJson()) {
            return response()->json($respuesta);
        }

        return redirect()->back()->with('success', $respuesta['message']);
    }

    /**
     * Eliminar una sala (Estándar REST - DELETE /api/salas/{id}).
     */
    public function destroy($id)
    {
        return $this->procesarEliminacion($id);
    }

    /**
     * Eliminar una sala por ID (Uso general - POST /api/salas/eliminar con campo 'id').
     */
    public function eliminar(Request $request)
    {
        return $this->procesarEliminacion($request->id, $request);
    }

    /**
     * Lógica unificada para eliminar una sala.
     */
    protected function procesarEliminacion($id, Request $request = null)
    {
        $sala = Sala::find($id);

        if (!$sala) {
            $mensaje = ['status' => 'error', 'message' => 'No se encontró la sala.'];
            $status = 404;
            
            return ($request && $request->expectsJson()) || !$request
                ? response()->json($mensaje, $status)
                : redirect()->back()->with('error', $mensaje['message']);
        }

        $nombreSala = $sala->nombre;
        $sala->delete();
        
        \App\Models\Log::create([
            'fecha' => now(),
            'nombre' => session('nombre', 'Admin'),
            'rol' => session('rol', 'administrador'),
            'accion' => "Eliminó la sala: {$nombreSala} (ID: {$id})"
        ]);

        $respuesta = ['status' => 'success', 'message' => 'Sala eliminada correctamente.'];

        if (($request && $request->expectsJson()) || !$request) {
            return response()->json($respuesta);
        }

        return redirect()->back()->with('success', $respuesta['message']);
    }
}