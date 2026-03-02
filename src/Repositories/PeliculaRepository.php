<?php

declare(strict_types=1);

namespace App\Repositories;

use PDO;
use App\Models\Pelicula;

class PeliculaRepository {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function obtenerTodas(): array {
        // Seleccionamos solo las activas para el catálogo
        $sql = "SELECT * FROM peliculas WHERE estado = 'activa' ORDER BY idPelicula DESC";
        $stmt = $this->db->query($sql);
        
        $peliculas = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $peliculas[] = new Pelicula(
                $row['titulo'],
                (int)$row['duracion'],
                $row['genero'],
                $row['clasificacion'],
                $row['idioma'],
                $row['sinopsis'],
                $row['imagenPoster'],
                (int)$row['idPelicula']
            );
        }
        return $peliculas;
    }

    public function guardar(Pelicula $pelicula): bool {
        $sql = "INSERT INTO peliculas (titulo, sinopsis, duracion, genero, clasificacion, idioma, imagenPoster) 
                VALUES (:titulo, :sinopsis, :duracion, :genero, :clasificacion, :idioma, :imagenPoster)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':titulo' => $pelicula->getTitulo(),
            ':sinopsis' => $pelicula->getSinopsis(),
            ':duracion' => $pelicula->getDuracion(),
            ':genero' => $pelicula->getGenero(),
            ':clasificacion' => $pelicula->getClasificacion(),
            ':idioma' => $pelicula->getIdioma(),
            ':imagenPoster' => $pelicula->getImagenPoster()
        ]);
    }

    public function eliminar(int $idPelicula): bool {
        // 1. (Opcional pero muy recomendado) Borrar también la imagen física de la carpeta
        $stmtImg = $this->db->prepare("SELECT imagenPoster FROM peliculas WHERE idPelicula = :id");
        $stmtImg->execute([':id' => $idPelicula]);
        $pelicula = $stmtImg->fetch(PDO::FETCH_ASSOC);
        
        if ($pelicula && !empty($pelicula['imagenPoster'])) {
            // Buscamos la ruta exacta del archivo
            $rutaDestino = dirname(__DIR__, 2) . '/public/img/' . $pelicula['imagenPoster'];
            
            // Si el archivo existe en la carpeta y no es un link de internet, lo borramos
            if (file_exists($rutaDestino) && !str_starts_with($pelicula['imagenPoster'], 'http')) {
                unlink($rutaDestino); 
            }
        }

        // 2. BORRADO FÍSICO DE LA BASE DE DATOS
        $sql = "DELETE FROM peliculas WHERE idPelicula = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $idPelicula]);
    }
    public function editar(Pelicula $pelicula): bool {
        // Actualizamos los textos
        $sql = "UPDATE peliculas SET titulo = :titulo, sinopsis = :sinopsis, duracion = :duracion, 
                genero = :genero, clasificacion = :clasificacion, idioma = :idioma 
                WHERE idPelicula = :id";
        $stmt = $this->db->prepare($sql);
        $actualizado = $stmt->execute([
            ':titulo' => $pelicula->getTitulo(), ':sinopsis' => $pelicula->getSinopsis(),
            ':duracion' => $pelicula->getDuracion(), ':genero' => $pelicula->getGenero(),
            ':clasificacion' => $pelicula->getClasificacion(), ':idioma' => $pelicula->getIdioma(),
            ':id' => $pelicula->getId()
        ]);

        // Si subiste una imagen nueva al editar, también la actualizamos
        if ($actualizado && $pelicula->getImagenPoster() !== null) {
            $sqlImg = "UPDATE peliculas SET imagenPoster = :imagen WHERE idPelicula = :id";
            $stmtImg = $this->db->prepare($sqlImg);
            $stmtImg->execute([':imagen' => $pelicula->getImagenPoster(), ':id' => $pelicula->getId()]);
        }
        return $actualizado;
    }
}