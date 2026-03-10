<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empleado;
use App\Models\Log;

class EmpleadoController extends Controller {

    public function getDatos() {
        try {
            // Filtramos solo los roles 1 (Admin) y 2 (Cajero)
            $empleados = Empleado::whereIn('idRol', [1, 2])
                ->get()
                ->map(function($e) {
                    // Traducimos el ID a texto para tu tabla de la web
                    $e->rol = ($e->idRol == 1) ? 'administrador' : 'cajero';
                    return $e;
                });

            // Logs (puedes dejarlo así o crear la tabla logs más tarde)
            $logs = []; 
            try {
                $logs = \App\Models\Log::orderBy('fecha', 'desc')->take(50)->get();
            } catch (\Exception $e) { $logs = []; }

            return response()->json([
                'empleados' => $empleados,
                'logs' => $logs
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }  

    public function gestionar(Request $request) {
        if ($request->accion_tipo === 'crear') {
            // Creamos usando el nombre exacto que tiene tu base de datos
            Empleado::create([
                'CI' => $request->CI,
                'nombre' => $request->nombre,
                'correo' => $request->correo,
                'contrasena' => $request->contrasena, 
                'idRol' => ($request->rol === 'administrador') ? 1 : 2,
                'estado' => 'activo'
            ]);
            
            \App\Models\Log::create([
                'fecha' => now(),
                'nombre' => session('nombre', 'Admin'),
                'rol' => session('rol', 'administrador'),
                'accion' => "Creó un nuevo empleado: {$request->nombre} (CI: {$request->CI}, Rol: {$request->rol})"
            ]);

            return response()->json(['status' => 'success', 'message' => 'Empleado creado']);
        }

        if ($request->accion_tipo === 'cambiar_estado') {
            $emp = Empleado::where('CI', $request->CI)->first();
            if ($emp) {
                $nombreEmp = $emp->nombre;
                $emp->estado = $request->estado;
                $emp->save();
                
                \App\Models\Log::create([
                    'fecha' => now(),
                    'nombre' => session('nombre', 'Admin'),
                    'rol' => session('rol', 'administrador'),
                    'accion' => "Cambió el estado del empleado: {$nombreEmp} a {$request->estado}"
                ]);

                return response()->json(['status' => 'success', 'message' => 'Estado actualizado']);
            }
        }
        return response()->json(['status' => 'error', 'message' => 'Acción no encontrada']);
    }
}