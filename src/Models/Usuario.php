<?php

declare(strict_types=1);

namespace App\Models;

class Usuario {
    public function __construct(
        private string $CI,
        private string $nombre,
        private string $correo,
        private string $contrasena,
        private string $rol
    ) {}

    // Getters
    public function getCI(): string { return $this->CI; }
    public function getNombre(): string { return $this->nombre; }
    public function getCorreo(): string { return $this->correo; }
    public function getContrasena(): string { return $this->contrasena; }
    public function getRol(): string { return $this->rol; }
}