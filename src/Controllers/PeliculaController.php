<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\PeliculaRepository;
use App\Models\Pelicula;

class PeliculaController {
    private PeliculaRepository $repository;

    public function __construct(PeliculaRepository $repository) {
        $this->repository = $repository;
    }

    public function listarPeliculas(): array {
        return $this->repository->obtenerTodas();
    }

    public function crearPelicula(array $datos, array $archivos): array {
        try {
            $nombreImagen = 'default.jpg';
            
            // 1. Lógica blindada para guardar la imagen
            if (isset($archivos['imagen']) && $archivos['imagen']['error'] === UPLOAD_ERR_OK) {
                $nombreImagen = $archivos['imagen']['name'];
                
                // Usamos dirname() para subir dos carpetas (desde src/Controllers hasta la raíz) y luego entramos a public/img
                $rutaDestino = dirname(__DIR__, 2) . '/public/img/' . $nombreImagen;
                
                // Movemos el archivo físico a tu carpeta
                if (!move_uploaded_file($archivos['imagen']['tmp_name'], $rutaDestino)) {
                    throw new \Exception("No se pudo guardar la imagen en la carpeta public/img/.");
                }
            }

            // 2. Guardamos en la Base de Datos
            $pelicula = new Pelicula(
                $datos['titulo'], (int)$datos['duracion'], $datos['genero'],
                $datos['clasificacion'], $datos['idioma'], $datos['sinopsis'],
                $nombreImagen
            );

            if ($this->repository->guardar($pelicula)) {
                return ["status" => "success", "message" => "Película agregada con éxito."];
            }
            throw new \Exception("Error al guardar en la base de datos.");
            
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function editarPelicula(array $datos, array $archivos): array {
        try {
            $nombreImagen = null; // Si no sube nada, se mantiene la imagen que ya tenía
            
            // Si el usuario sube una nueva imagen al editar
            if (isset($archivos['imagen']) && $archivos['imagen']['error'] === UPLOAD_ERR_OK) {
                $nombreImagen = $archivos['imagen']['name'];
                $rutaDestino = dirname(__DIR__, 2) . '/public/img/' . $nombreImagen;
                
                move_uploaded_file($archivos['imagen']['tmp_name'], $rutaDestino);
            }

            $pelicula = new Pelicula(
                $datos['titulo'], (int)$datos['duracion'], $datos['genero'],
                $datos['clasificacion'], $datos['idioma'], $datos['sinopsis'],
                $nombreImagen, (int)$datos['id']
            );

            if ($this->repository->editar($pelicula)) {
                return ["status" => "success", "message" => "Película actualizada con éxito."];
            }
            throw new \Exception("Error al actualizar la base de datos.");
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function eliminarPelicula(int $id): array {
        if ($this->repository->eliminar($id)) {
            return ["status" => "success", "message" => "Película eliminada correctamente."];
        }
        return ["status" => "error", "message" => "No se pudo realizar la eliminación."];
    }
}