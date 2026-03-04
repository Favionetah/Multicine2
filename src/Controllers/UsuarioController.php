<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\UsuarioRepository;
use Exception;

// ═══════════════════════════════════════════════════════════════
// PATRÓN STRATEGY
// ───────────────────────────────────────────────────────────────
// Interfaz: contrato que toda estrategia de validación debe cumplir
// ═══════════════════════════════════════════════════════════════

interface ValidacionStrategy {
    /**
     * Valida $datos y retorna los campos limpios.
     * Lanza Exception si algo falla.
     */
    public function validar(array $datos): array;
}

// ───────────────────────────────────────────────────────────────
// Estrategia concreta #1 — Validación de Registro
// ───────────────────────────────────────────────────────────────

class ValidacionRegistroStrategy implements ValidacionStrategy {
    public function __construct(private UsuarioRepository $repository) {
    }

    public function validar(array $datos): array {
        $CI         = trim($datos['CI']         ?? '');
        $nombre     = trim($datos['nombre']     ?? '');
        $correo     = trim($datos['correo']     ?? '');
        $contrasena =      $datos['contrasena'] ?? '';
        $telefono   = trim($datos['telefono']   ?? '');

        if (!$CI || !$nombre || !$correo || !$contrasena || !$telefono) {
            throw new Exception("Todos los campos son obligatorios.");
        }

        if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $nombre)) {
            throw new Exception("El nombre solo debe contener letras. Sin números ni símbolos.");
        }

        if (!preg_match('/^[0-9]{8}$/', $telefono)) {
            throw new Exception("El número de teléfono debe tener exactamente 8 dígitos.");
        }

        if ($this->repository->buscarPorCorreo($correo)) {
            throw new Exception("Este correo electrónico ya está registrado.");
        }

        // Retorna datos limpios y listos para usar
        return compact('CI', 'nombre', 'correo', 'contrasena', 'telefono');
    }
}

// ───────────────────────────────────────────────────────────────
// Estrategia concreta #2 — Validación de Login
// ───────────────────────────────────────────────────────────────

class ValidacionLoginStrategy implements ValidacionStrategy {
    public function validar(array $datos): array {
        $correo     = trim($datos['correo']     ?? '');
        $contrasena =      $datos['contrasena'] ?? '';

        if (empty($correo) || empty($contrasena)) {
            throw new Exception("El correo y la contraseña son obligatorios.");
        }

        // Bonus: validación de formato que antes no existía
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("El formato del correo no es válido.");
        }

        return compact('correo', 'contrasena');
    }
}

// ═══════════════════════════════════════════════════════════════
// CONTROLADOR — ahora limpio, sin if de validación
// ═══════════════════════════════════════════════════════════════

class UsuarioController {
    public function __construct(
        private UsuarioRepository $repository
    ) {
    }

    // ── US-01 Registro ──────────────────────────────────────────

    public function registrarCliente(array $datos): array {
        try {
            // Selecciona y ejecuta la estrategia de registro
            $estrategia = new ValidacionRegistroStrategy($this->repository);
            $datosLimpios = $estrategia->validar($datos); // ← toda la validación aquí

            if ($this->repository->crearCliente($datosLimpios)) {
                return ["status" => "success", "message" => "Cuenta creada con éxito. Ya puedes iniciar sesión."];
            }

            throw new Exception("Error interno al crear la cuenta.");
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    // ── Login ───────────────────────────────────────────────────

    public function login(array $datos): array {
        try {
            // Selecciona y ejecuta la estrategia de login
            $estrategia = new ValidacionLoginStrategy();
            $datosLimpios = $estrategia->validar($datos); // ← toda la validación aquí

            $usuario = $this->repository->buscarPorCorreo($datosLimpios['correo']);

            if (!$usuario) {
                throw new Exception("Credenciales incorrectas.");
            }

            $passValida = password_verify($datosLimpios['contrasena'], $usuario->getContrasena())
                || $datosLimpios['contrasena'] === $usuario->getContrasena();

            if (!$passValida) {
                throw new Exception("Credenciales incorrectas.");
            }

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            session_regenerate_id(true);

            $_SESSION['CI']     = $usuario->getCI();
            $_SESSION['nombre'] = $usuario->getNombre();
            $_SESSION['rol']    = $usuario->getRol();

            return [
                "status"  => "success",
                "rol"     => $usuario->getRol(),
                "message" => "Bienvenido " . $usuario->getNombre()
            ];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    // ── Logout ──────────────────────────────────────────────────

    public function logout(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];
        session_destroy();

        header("Location: index.php");
        exit();
    }
}
