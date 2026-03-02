<?php

declare(strict_types=1);

namespace App\Models;

use JsonSerializable;

class Pelicula implements JsonSerializable {
    private ?int $idPelicula;
    private string $titulo;
    private int $duracion;
    private string $genero;
    private string $clasificacion;
    private string $idioma;
    private string $sinopsis;
    private ?string $imagenPoster;

    public function __construct(
        string $titulo,
        int $duracion,
        string $genero,
        string $clasificacion,
        string $idioma,
        string $sinopsis,
        ?string $imagenPoster = null,
        ?int $idPelicula = null
    ) {
        $this->idPelicula = $idPelicula;
        $this->titulo = $titulo;
        $this->duracion = $duracion;
        $this->genero = $genero;
        $this->clasificacion = $clasificacion;
        $this->idioma = $idioma;
        $this->sinopsis = $sinopsis;
        $this->imagenPoster = $imagenPoster;
    }

    // Getters para el Repositorio
    public function getId(): ?int { return $this->idPelicula; }
    public function getTitulo(): string { return $this->titulo; }
    public function getDuracion(): int { return $this->duracion; }
    public function getGenero(): string { return $this->genero; }
    public function getClasificacion(): string { return $this->clasificacion; }
    public function getIdioma(): string { return $this->idioma; }
    public function getSinopsis(): string { return $this->sinopsis; }
    public function getImagenPoster(): ?string { return $this->imagenPoster; }

    /**
     * Solución al error UNDEFINED:
     * Especifica cómo se debe serializar el objeto a JSON.
     */
    public function jsonSerialize(): mixed {
        return [
            'idPelicula'   => $this->idPelicula,
            'titulo'       => $this->titulo,
            'duracion'     => $this->duracion,
            'genero'       => $this->genero,
            'clasificacion' => $this->clasificacion,
            'idioma'       => $this->idioma,
            'sinopsis'     => $this->sinopsis,
            'imagenPoster' => $this->imagenPoster
        ];
    }
}