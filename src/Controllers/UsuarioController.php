<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\UsuarioRepository;
use Exception;

// --- LA ESTRATEGIA (La interfaz) ---
interface ValidacionStrategy {
    public function validar(array $datos, UsuarioRepository $repository): void;
}

// --- IMPLEMENTACIÓN DE LA ESTRATEGIA ---
class RegistroClienteStrategy implements ValidacionStrategy {
    public function validar(array $datos, UsuarioRepository $repository): void {
        $CI = trim($datos['CI'] ?? '');
        $nombre = trim($datos['nombre'] ?? '');
        $correo = trim($datos['correo'] ?? '');
        $contrasena = $datos['contrasena'] ?? '';
        $telefono = trim($datos['telefono'] ?? '');

        if (!$CI || !$nombre || !$correo || !$contrasena || !$telefono) {
            throw new Exception("Todos los campos son obligatorios.");
        }

        if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $nombre)) {
            throw new Exception("El nombre solo debe contener letras.");
        }

        if (!preg_match('/^[0-9]{8}$/', $telefono)) {
            throw new Exception("El teléfono debe tener 8 dígitos.");
        }

        if ($repository->buscarPorCorreo($correo)) {
            throw new Exception("Este correo ya está registrado.");
        }
    }
}

// --- EL CONTROLADOR (Contexto del Strategy) ---
class UsuarioController {
    private ValidacionStrategy $validacionStrategy;

    public function __construct(
        private UsuarioRepository $repository
    ) {
        // Por defecto asignamos la estrategia de registro
        $this->validacionStrategy = new RegistroClienteStrategy();
    }

    // Método para cambiar la estrategia en tiempo de ejecución
    public function setStrategy(ValidacionStrategy $strategy): void {
        $this->validacionStrategy = $strategy;
    }

    public function registrarCliente(array $datos): array {
        try {
            // delegamos la validación a la ESTRATEGIA
            $this->validacionStrategy->validar($datos, $this->repository);

            if ($this->repository->crearCliente($datos)) {
                return ["status" => "success", "message" => "Cuenta creada con éxito."];
            }
            throw new Exception("Error interno al crear la cuenta.");
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function login(array $datos): array {
        // ... (El resto del código de login se mantiene igual)
        // Pero podrías crear una "LoginStrategy" para limpiar este método también
        try {
            $correo = trim($datos['correo'] ?? '');
            $contrasenaIngresada = $datos['contrasena'] ?? '';

            if (empty($correo) || empty($contrasenaIngresada)) {
                throw new Exception("El correo y la contraseña son obligatorios.");
            }

            $usuario = $this->repository->buscarPorCorreo($correo);
            if (!$usuario) throw new Exception("Credenciales incorrectas.");

            $passValida = password_verify($contrasenaIngresada, $usuario->getContrasena()) || $contrasenaIngresada === $usuario->getContrasena();
            if (!$passValida) throw new Exception("Credenciales incorrectas.");

            if (session_status() === PHP_SESSION_NONE) session_start();

            $_SESSION['CI'] = $usuario->getCI();
            $_SESSION['nombre'] = $usuario->getNombre();
            $_SESSION['rol'] = $usuario->getRol();

            return ["status" => "success", "rol" => $usuario->getRol(), "message" => "Bienvenido " . $usuario->getNombre()];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }

    public function logout(): void {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy();
        header("Location: index.php");
        exit();
    }
}
