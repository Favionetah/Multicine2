<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConfiguracionController extends Controller
{
    private $filePath = 'tarifas.json';

    /**
     * Obtener los descuentos desde el archivo JSON.
     */
    public function getTarifas()
    {
        if (!Storage::disk('local')->exists($this->filePath)) {
            $default = [
                'nino' => 0,
                'miercoles' => 0,
                'socio' => 0
            ];
            Storage::disk('local')->put($this->filePath, json_encode($default));
        }

        $config = json_decode(Storage::disk('local')->get($this->filePath), true);

        return response()->json($config);
    }

    /**
     * Actualizar los descuentos guardando en el archivo JSON.
     */
    public function updateTarifas(Request $request)
    {
        $config = [
            'nino' => (int)$request->nino,
            'miercoles' => (int)$request->miercoles,
            'socio' => (int)$request->socio
        ];

        Storage::disk('local')->put($this->filePath, json_encode($config));

        \App\Models\Log::create([
            'fecha' => now(),
            'nombre' => session('nombre', 'Admin'),
            'rol' => session('rol', 'administrador'),
            'accion' => "Actualizó las tarifas (Niño: {$request->nino}%, Miér: {$request->miercoles}%, Socio: {$request->socio}%)"
        ]);

        return response()->json([
            'status' => 'success', 
            'message' => 'Tarifas actualizadas correctamente',
            'data' => $config
        ]);
    }
}