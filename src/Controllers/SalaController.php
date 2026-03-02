<?php
namespace App\Controllers;
use App\Repositories\SalaRepository;

class SalaController {
    private SalaRepository $repository;
    public function __construct(SalaRepository $repository) { $this->repository = $repository; }

    public function listarSalas(): array { return $this->repository->obtenerTodas(); }

    public function crearSala(array $datos): array {
        try {
            if ($this->repository->guardar($datos)) return ["status" => "success", "message" => "Sala creada con éxito."];
            throw new \Exception("Error al guardar en BD.");
        } catch (\Exception $e) { return ["status" => "error", "message" => $e->getMessage()]; }
    }

    public function editarSala(array $datos): array {
        try {
            if ($this->repository->editar($datos)) return ["status" => "success", "message" => "Sala actualizada."];
            throw new \Exception("Error al actualizar la base de datos.");
        } catch (\Exception $e) { return ["status" => "error", "message" => $e->getMessage()]; }
    }

    public function eliminarSala(int $id): array {
        if ($this->repository->eliminar($id)) {
            return ["status" => "success", "message" => "Sala eliminada correctamente."];
        }
        return ["status" => "error", "message" => "No se pudo eliminar la sala."];
    }
}