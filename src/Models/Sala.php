<?php

declare(strict_types=1);

namespace App\Models;

class Sala {
    public function __construct(
        private int $numero,
        private string $tipoPantalla,
        private int $capacidadTotal,
        private string $estado = 'activa',
        private ?int $idSala = null
    ) {}

    // Getters
    public function getId(): ?int { return $this->idSala; }
    public function getNumero(): int { return $this->numero; }
    public function getTipoPantalla(): string { return $this->tipoPantalla; }
    public function getCapacidadTotal(): int { return $this->capacidadTotal; }
    public function getEstado(): string { return $this->estado; }
}