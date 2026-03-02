<?php

declare(strict_types=1);

namespace App\Models;

class Funcion {
    public function __construct(
        private int $idPelicula,
        private int $idSala,
        private string $fechaFuncion,
        private string $horaInicio,
        private string $horaFin,
        private float $precioBase,
        private ?int $idFuncion = null
    ) {}

    // Getters
    public function getId(): ?int { return $this->idFuncion; }
    public function getIdPelicula(): int { return $this->idPelicula; }
    public function getIdSala(): int { return $this->idSala; }
    public function getFechaFuncion(): string { return $this->fechaFuncion; }
    public function getHoraInicio(): string { return $this->horaInicio; }
    public function getHoraFin(): string { return $this->horaFin; }
    public function getPrecioBase(): float { return $this->precioBase; }
}