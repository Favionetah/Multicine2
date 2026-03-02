<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Reserva;
use App\Repositories\ReservaRepository;
use Exception;

class ReservaController {
    public function __construct(
        private ReservaRepository $repository
    ) {}

    public function crearVenta(array $datosPost): array {
        try {
            // Validaciones básicas
            if (empty($datosPost['CICliente']) || empty($datosPost['idFuncion']) || empty($datosPost['asientos'])) {
                throw new Exception("Faltan datos obligatorios para procesar la venta.");
            }

            $asientosIds = $datosPost['asientos']; // Array con los IDs de los asientos seleccionados
            $montoTotal = (float)$datosPost['montoTotal'];

            $reserva = new Reserva(
                $datosPost['CICliente'],
                (int)$datosPost['idFuncion'],
                $montoTotal,
                $datosPost['CIEmpleado'] ?? null // Puede ser null si compra el cliente online
            );

            if ($this->repository->procesarVenta($reserva, $asientosIds)) {
                return [
                    "status" => "success", 
                    "message" => "✅ Venta registrada y tickets generados exitosamente."
                ];
            } else {
                throw new Exception("Error desconocido al registrar la venta.");
            }

        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }
}