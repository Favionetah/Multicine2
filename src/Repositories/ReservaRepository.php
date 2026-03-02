<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;
use App\Models\Reserva;
use Exception;

class ReservaRepository {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Registra una reserva y genera los tickets de manera transaccional
     */
    public function procesarVenta(Reserva $reserva, array $asientosIds): bool {
        try {
            // Iniciamos la transacción de seguridad
            $this->db->beginTransaction();

            // 1. Insertar la Reserva principal
            $sqlReserva = "INSERT INTO reservas (CICliente, idFuncion, CIEmpleado, estado, montoTotal) 
                           VALUES (:cliente, :funcion, :empleado, :estado, :monto)";
            $stmtReserva = $this->db->prepare($sqlReserva);
            $stmtReserva->execute([
                ':cliente' => $reserva->getCICliente(),
                ':funcion' => $reserva->getIdFuncion(),
                ':empleado' => $reserva->getCIEmpleado(),
                ':estado' => $reserva->getEstado(),
                ':monto' => $reserva->getMontoTotal()
            ]);

            $idReserva = (int)$this->db->lastInsertId();

            // 2. Generar un Ticket por cada asiento seleccionado y cambiar el estado del asiento
            $precioPorBoleto = $reserva->getMontoTotal() / count($asientosIds);
            
            $sqlTicket = "INSERT INTO tickets (idReserva, idAsiento, codigoQR, precioFinal) 
                          VALUES (:reserva, :asiento, :qr, :precio)";
            $stmtTicket = $this->db->prepare($sqlTicket);

            $sqlAsiento = "UPDATE asientos SET estado = 'ocupado' WHERE idAsiento = :idAsiento";
            $stmtAsiento = $this->db->prepare($sqlAsiento);

            foreach ($asientosIds as $idAsiento) {
                // Generar un código QR único ficticio basado en el tiempo y el asiento
                $codigoQR = md5(uniqid("QR_", true) . $idAsiento);

                $stmtTicket->execute([
                    ':reserva' => $idReserva,
                    ':asiento' => $idAsiento,
                    ':qr' => $codigoQR,
                    ':precio' => $precioPorBoleto
                ]);

                // Marcar el asiento como ocupado
                $stmtAsiento->execute([':idAsiento' => $idAsiento]);
            }

            // Si todo salió bien, confirmamos los cambios en la base de datos
            $this->db->commit();
            return true;

        } catch (Exception $e) {
            // Si hubo cualquier error, deshacemos TODO para no cobrar sin dar boleto
            $this->db->rollBack();
            throw new Exception("Error al procesar la venta: " . $e->getMessage());
        }
    }
}