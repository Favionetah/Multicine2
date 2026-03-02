<?php

declare(strict_types=1);

namespace App\Models;

class Reserva {
    public function __construct(
        private string $CICliente,
        private int $idFuncion,
        private float $montoTotal,
        private ?string $CIEmpleado = null,
        private string $estado = 'confirmada',
        private ?int $idReserva = null
    ) {}

    // Getters
    public function getId(): ?int { return $this->idReserva; }
    public function getCICliente(): string { return $this->CICliente; }
    public function getIdFuncion(): int { return $this->idFuncion; }
    public function getCIEmpleado(): ?string { return $this->CIEmpleado; }
    public function getMontoTotal(): float { return $this->montoTotal; }
    public function getEstado(): string { return $this->estado; }
}