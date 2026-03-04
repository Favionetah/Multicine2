<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;
use App\Models\Usuario;

class UsuarioRepository {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Busca un usuario por su correo electrónico
     */
    public function buscarPorCorreo(string $correo): ?Usuario {
        $sql = "SELECT * FROM usuarios WHERE correo = :correo LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':correo' => $correo]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            return new Usuario(
                (string)$row['CI'],
                (string)$row['nombre'],
                (string)$row['correo'],
                (string)$row['contrasena'],
                (string)$row['rol']
            );
        }

        return null;
    }

    /**
     * Crea un nuevo cliente en la base de datos PREPARED STATEMENTS
     */
    public function crearCliente(array $datos): bool {
        $sql = "INSERT INTO usuarios (CI, nombre, correo, contrasena, telefono, rol) 
                VALUES (:CI, :nombre, :correo, :contrasena, :telefono, 'cliente')";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':CI' => $datos['CI'],
            ':nombre' => $datos['nombre'],
            ':correo' => $datos['correo'],
            ':contrasena' => password_hash($datos['contrasena'], PASSWORD_DEFAULT),
            ':telefono' => $datos['telefono']
        ]);
    }
}
