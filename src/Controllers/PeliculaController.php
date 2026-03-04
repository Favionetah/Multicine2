<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\PeliculaRepository;
use App\Models\Pelicula;

// ═══════════════════════════════════════════════════════════════
// PATRÓN OBSERVER
// ───────────────────────────────────────────────────────────────
// Interfaz que todo observador debe cumplir
// ═══════════════════════════════════════════════════════════════

interface PeliculaObserver {
    public function actualizar(string $evento, mixed $datos): void;
}

// ───────────────────────────────────────────────────────────────
// Observador concreto #1 — Logger
// Se dispara en CUALQUIER evento y deja registro del suceso
// ───────────────────────────────────────────────────────────────

class LoggerObserver implements PeliculaObserver {
    public function actualizar(string $evento, mixed $datos): void {
        $timestamp = date('Y-m-d H:i:s');
        $detalle   = is_array($datos) ? json_encode($datos) : (string)$datos;

        // En producción esto iría a un archivo de log real
        error_log("[{$timestamp}] EVENTO: {$evento} | DATOS: {$detalle}");
    }
}

// ───────────────────────────────────────────────────────────────
// Observador concreto #2 — Caché
// Invalida la caché cuando el catálogo cambia
// ───────────────────────────────────────────────────────────────

class CacheObserver implements PeliculaObserver {
    private static array $cache = [];

    public function actualizar(string $evento, mixed $datos): void {
        // Si se creó, editó o eliminó una película, la caché queda obsoleta
        if (in_array($evento, ['pelicula_creada', 'pelicula_editada', 'pelicula_eliminada'])) {
            self::$cache = [];
            // En producción: apcu_clear_cache() o similar
        }
    }
}

// ═══════════════════════════════════════════════════════════════
// SUJETO — el que emite los eventos y gestiona observadores
// ═══════════════════════════════════════════════════════════════

trait Observable {
    private array $observadores = [];

    public function suscribir(PeliculaObserver $observador): void {
        $this->observadores[] = $observador;
    }

    public function notificar(string $evento, mixed $datos): void {
        foreach ($this->observadores as $observador) {
            $observador->actualizar($evento, $datos);
        }
    }
}

// ═══════════════════════════════════════════════════════════════
// CONTROLADOR — usa el trait Observable para emitir eventos
// ═══════════════════════════════════════════════════════════════

class PeliculaController {
    use Observable; // ← aquí entra el patrón

    private PeliculaRepository $repository;

    public function __construct(PeliculaRepository $repository) {
        $this->repository = $repository;

        // Registrar observadores — se pueden agregar más sin tocar nada más
        $this->suscribir(new LoggerObserver());
        $this->suscribir(new CacheObserver());
    }

    public function listarPeliculas(): array {
        return $this->repository->obtenerTodas();
    }

    public function crearPelicula(array $datos, array $archivos): array {
        try {
            $nombreImagen = 'default.jpg';

            if (isset($archivos['imagen']) && $archivos['imagen']['error'] === UPLOAD_ERR_OK) {
                $nombreImagen = $archivos['imagen']['name'];
                $rutaDestino  = dirname(__DIR__, 2) . '/public/img/' . $nombreImagen;

                if (!move_uploaded_file($archivos['imagen']['tmp_name'], $rutaDestino)) {
                    throw new \Exception("No se pudo guardar la imagen en la carpeta public/img/.");
                }
            }

            $pelicula = new Pelicula(
                $datos['titulo'],
                (int)$datos['duracion'],
                $datos['genero'],
                $datos['clasificacion'],
                $datos['idioma'],
                $datos['sinopsis'],
                $nombreImagen
            );

            if ($this->repository->guardar($pelicula)) {
                // 🔔 Notifica a todos los observadores
                $this->notificar('pelicula_creada', $datos['titulo']);
                return ["status" => "success", "message" => "Película agregada con éxito."];
            }

            throw new \Exception("Error al guardar en la base de datos.");
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function editarPelicula(array $datos, array $archivos): array {
        try {
            $nombreImagen = null;

            if (isset($archivos['imagen']) && $archivos['imagen']['error'] === UPLOAD_ERR_OK) {
                $nombreImagen = $archivos['imagen']['name'];
                $rutaDestino  = dirname(__DIR__, 2) . '/public/img/' . $nombreImagen;

                move_uploaded_file($archivos['imagen']['tmp_name'], $rutaDestino);
            }

            $pelicula = new Pelicula(
                $datos['titulo'],
                (int)$datos['duracion'],
                $datos['genero'],
                $datos['clasificacion'],
                $datos['idioma'],
                $datos['sinopsis'],
                $nombreImagen,
                (int)$datos['id']
            );

            if ($this->repository->editar($pelicula)) {
                // 🔔 Notifica a todos los observadores
                $this->notificar('pelicula_editada', ['id' => $datos['id'], 'titulo' => $datos['titulo']]);
                return ["status" => "success", "message" => "Película actualizada con éxito."];
            }

            throw new \Exception("Error al actualizar la base de datos.");
        } catch (\Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function eliminarPelicula(int $id): array {
        if ($this->repository->eliminar($id)) {
            // 🔔 Notifica a todos los observadores
            $this->notificar('pelicula_eliminada', $id);
            return ["status" => "success", "message" => "Película eliminada correctamente."];
        }

        return ["status" => "error", "message" => "No se pudo realizar la eliminación."];
    }
}
