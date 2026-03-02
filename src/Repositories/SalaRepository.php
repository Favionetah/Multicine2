<?php

namespace App\Repositories;

use PDO;

class SalaRepository {
    private PDO $db;
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function obtenerTodas(): array {
        // CORRECCIÓN AQUÍ: ORDER BY idSala
        $stmt = $this->db->query("SELECT * FROM salas ORDER BY idSala ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function guardar(array $datos): bool {
        $capacidadTotal = (int)$datos['filas'] * (int)$datos['columnas'];
        $tipo = $datos['tipo'] ?? 'classic';

        $imagenes = ['xl' => 'sala_xl.jpg', 'plus' => 'sala_plus.jpg', '4d' => 'sala_4d.jpg', 'classic' => 'sala_classic.jpg'];
        $imagen = $imagenes[$tipo] ?? 'sala_classic.jpg';

        $sql = "INSERT INTO salas (nombre, capacidadTotal, filas, columnas, precio, imagen, tipo) 
                VALUES (:nombre, :capacidadTotal, :filas, :columnas, :precio, :imagen, :tipo)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre' => $datos['nombre'],
            ':capacidadTotal' => $capacidadTotal,
            ':filas' => $datos['filas'],
            ':columnas' => $datos['columnas'],
            ':precio' => $datos['precio'],
            ':imagen' => $imagen,
            ':tipo' => $tipo
        ]);
    }

    public function editar(array $datos): bool {
        $capacidadTotal = (int)$datos['filas'] * (int)$datos['columnas'];
        $tipo = $datos['tipo'] ?? 'classic';

        $imagenes = ['xl' => 'sala_xl.jpg', 'plus' => 'sala_plus.jpg', '4d' => 'sala_4d.jpg', 'classic' => 'sala_classic.jpg'];
        $imagen = $imagenes[$tipo] ?? 'sala_classic.jpg';

        // CORRECCIÓN AQUÍ: WHERE idSala = :id
        $sql = "UPDATE salas SET nombre = :nombre, capacidadTotal = :capacidadTotal, 
                filas = :filas, columnas = :columnas, precio = :precio, imagen = :imagen, tipo = :tipo 
                WHERE idSala = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nombre' => $datos['nombre'],
            ':capacidadTotal' => $capacidadTotal,
            ':filas' => $datos['filas'],
            ':columnas' => $datos['columnas'],
            ':precio' => $datos['precio'],
            ':imagen' => $imagen,
            ':tipo' => $tipo,
            ':id' => $datos['id']
        ]);
    }

    public function eliminar(int $id): bool {
        // CORRECCIÓN AQUÍ: WHERE idSala = :id
        $stmt = $this->db->prepare("DELETE FROM salas WHERE idSala = :id");
        return $stmt->execute([':id' => $id]);
    }
}
