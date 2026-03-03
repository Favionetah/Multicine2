<?php

declare(strict_types=1);

namespace Config;

use PDO;
use PDOException;
use Exception;

class Database {
    private string $host = 'localhost';
    private string $db_name = 'multicinelp2';
    private string $username = 'root';
    private string $password = 'root';
    private ?PDO $conn = null;

    /**
     * Establece y retorna la conexión a la base de datos.
     * * @return PDO
     * @throws Exception Si hay un error de conexión
     */
    public function connect(): PDO {
        if ($this->conn === null) {
            try {
                $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lanza excepciones en errores SQL
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Devuelve arrays asociativos por defecto
                    PDO::ATTR_EMULATE_PREPARES   => false,                  // Mayor seguridad en sentencias preparadas
                ];

                $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            } catch (PDOException $e) {
                // Aquí usamos excepciones personalizadas como pide tu informe
                throw new Exception("Error de conexión a la base de datos: " . $e->getMessage());
            }
        }

        return $this->conn;
    }
}
