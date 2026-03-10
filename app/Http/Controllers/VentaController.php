<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function procesarVenta(Request $request)
    {
        try {
            // Recibimos los datos del fetch de JavaScript
            $totalPagado = floatval($request->total);
            $puntosUsados = intval($request->puntosUsados);
            $asientosArray = json_decode($request->asientos);
            
            // Convertimos el array de asientos a texto (Ej: "J4, J5")
            $asientosStr = implode(', ', $asientosArray);
            
            // Generamos un código de ticket único (Ej: TK-A9F3D)
            $codigoTicket = 'TK-' . strtoupper(substr(uniqid(), -5));

            // 1. Guardar la compra en la tabla 'compras'
            DB::table('compras')->insert([
                'CI_cliente' => $request->ciCliente,
                'idFuncion' => $request->idFuncion,
                'asientos' => $asientosStr,
                'total' => $totalPagado,
                'codigo_ticket' => $codigoTicket,
                'fecha_compra' => now()
            ]);

            // 1.5. ACTUALIZAR ASIENTOS EN LA TABLA 'FUNCIONES'
            // Esto es CRÍTICO para que los asientos aparezcan como ocupados en el mapa
            $funcion = DB::table('funciones')
                ->join('peliculas', 'funciones.idPelicula', '=', 'peliculas.idPelicula')
                ->where('funciones.idFuncion', $request->idFuncion)
                ->select('funciones.*', 'peliculas.titulo')
                ->first();
            
            if ($funcion) {
                // Obtenemos los asientos que ya estaban vendidos (quitando espacios vacíos)
                $existentes = $funcion->asientos_vendidos ? explode(',', $funcion->asientos_vendidos) : [];
                $existentes = array_map('trim', $existentes);
                $existentes = array_filter($existentes);

                // Combinamos con los nuevos (asientosArray ya es un array de JS decodificado)
                $nuevoSet = array_unique(array_merge($existentes, $asientosArray));
                $nuevoSetStr = implode(',', $nuevoSet);
                $cantidadBoletos = count($nuevoSet);

                DB::table('funciones')
                    ->where('idFuncion', $request->idFuncion)
                    ->update([
                        'asientos_vendidos' => $nuevoSetStr,
                        'boletos_vendidos' => $cantidadBoletos
                    ]);
                
                \Log::info("Asientos y boletos actualizados para función {$request->idFuncion}: {$nuevoSetStr} ({$cantidadBoletos})");

                // REGISTRO EN EL LOG DE ACTIVIDADES
                \App\Models\Log::create([
                    'fecha' => now(),
                    'nombre' => session('nombre', 'Cajero/Sistema'),
                    'rol' => session('rol', 'cajero'),
                    'accion' => "Nueva Venta Procesada: Película \"{$funcion->titulo}\" (ID: {$request->idFuncion}), Monto: Bs " . number_format($totalPagado, 2) . ", Asientos: {$asientosStr}"
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No se encontró la función seleccionada. Por favor, intente de nuevo.'
                ]);
            }

            // --- 2. LÓGICA DE PUNTOS BENEFICIO ---
            // Sumamos 1 punto por cada 30 Bs pagados (Usamos floor para redondear hacia abajo)
            $puntosGanados = floor($totalPagado / 30);

            // 3. Actualizamos la billetera del cliente en la tabla 'usuarios'
            // Restamos los puntos que usó como descuento y le sumamos los nuevos que acaba de ganar
            \Illuminate\Support\Facades\DB::table('usuarios')
                ->where('CI', $request->ciCliente)
                ->update([
                    'puntos' => \Illuminate\Support\Facades\DB::raw("puntos - $puntosUsados + $puntosGanados")
                ]);

            return response()->json([
                'status' => 'success',
                'codigo' => $codigoTicket,
                'puntos_ganados' => $puntosGanados
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Error al procesar el pago: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Buscar un ticket por su código para validación.
     */
    public function verificarTicket(Request $request)
    {
        $codigo = $request->codigo;

        if (!$codigo) {
            return response()->json(['status' => 'error', 'message' => 'Debe ingresar un código.'], 400);
        }

        $ticket = DB::table('compras')
            ->join('funciones', 'compras.idFuncion', '=', 'funciones.idFuncion')
            ->join('peliculas', 'funciones.idPelicula', '=', 'peliculas.idPelicula')
            ->join('salas', 'funciones.idSala', '=', 'salas.idSala')
            ->where('compras.codigo_ticket', strtoupper($codigo))
            ->select(
                'compras.*',
                'peliculas.titulo',
                'funciones.fechaFuncion',
                'funciones.horaInicio',
                'salas.nombre as sala'
            )
            ->first();

        if (!$ticket) {
            return response()->json(['status' => 'error', 'message' => 'Boleto no encontrado.'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $ticket
        ]);
    }
}