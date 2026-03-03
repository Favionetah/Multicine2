<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\FuncionRepository;

class FuncionController {
    private FuncionRepository $repository;

    public function __construct(FuncionRepository $repository) {
        $this->repository = $repository;
    }

    public function crearFuncion(array $datos): array {
        try {
            if (empty($datos['idPelicula']) || empty($datos['idSala']) || empty($datos['fechaFuncion'])) {
                throw new \Exception("Faltan datos requeridos.");
            }

            // MAGIA: Validación de fecha pasada
            $hoy = date('Y-m-d');
            if ($datos['fechaFuncion'] < $hoy) {
                throw new \Exception("No puedes programar funciones en fechas que ya pasaron.");
            }

            if ($this->repository->guardar($datos)) {
                return ["status" => "success", "message" => "Función programada con éxito."];
            }
            throw new \Exception("Error al guardar en la base de datos.");
            
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function listarFunciones(): array {
        return $this->repository->obtenerTodas();
    }

    public function editarFuncion(array $datos): array {
        try {
            if ($this->repository->editar($datos)) {
                return ["status" => "success", "message" => "Función actualizada con éxito."];
            }
            throw new \Exception("Error al actualizar la base de datos.");
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function eliminarFuncion(int $id): array {
        if ($this->repository->eliminar($id)) {
            return ["status" => "success", "message" => "Función eliminada correctamente."];
        }
        return ["status" => "error", "message" => "No se pudo eliminar la función."];
    }

    public function carteleraCajero(): array {
        return $this->repository->obtenerCarteleraCajero();
    }

    public function venderBoletos(array $datos): array {
        try {
            $asientos = json_decode($datos['asientos'], true);
            if (empty($asientos)) throw new \Exception("Debe seleccionar al menos un asiento.");

            // Agrupamos los datos incluyendo el CI del cliente si lo enviaron
            $datosVenta = [
                'idFuncion' => $datos['idFuncion'],
                'asientos' => $asientos,
                'ciCliente' => $datos['ciCliente'] ?? '0' 
            ];

            if ($this->repository->registrarVenta($datosVenta)) {
                return ["status" => "success", "message" => "Venta completada."];
            }
            throw new \Exception("Error al registrar la venta.");
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function comprarBoletosCliente(array $datos): array {
        try {
            if (session_status() === PHP_SESSION_NONE) session_start();
            if (!isset($_SESSION['CI'])) throw new \Exception("Debes iniciar sesión para comprar.");

            $asientos = json_decode($datos['asientos'], true);
            if (empty($asientos)) throw new \Exception("Seleccione al menos un asiento.");

            $codigo = $this->repository->registrarCompraCliente([
                'idFuncion' => $datos['idFuncion'],
                'asientos' => $asientos,
                'total' => $datos['total'],
                'CI' => $_SESSION['CI'] // Usamos el CI del usuario logueado automáticamente
            ]);

            return ["status" => "success", "codigo" => $codigo, "message" => "Compra exitosa."];
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function historialCliente(): array {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $ci = $_SESSION['CI'] ?? '';
        return $this->repository->obtenerHistorialCliente($ci);
    }

    
}