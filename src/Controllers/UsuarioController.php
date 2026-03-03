<?php


declare(strict_types=1);


namespace App\Controllers;


use App\Repositories\UsuarioRepository;
use Exception;


class UsuarioController {
    public function __construct(
        private UsuarioRepository $repository
    ) {
    }


    /**
     * Procesa el Registro de Cliente (US-01)
     */
    public function registrarCliente(array $datos): array {
        try {
            $CI = trim($datos['CI'] ?? '');
            $nombre = trim($datos['nombre'] ?? '');
            $correo = trim($datos['correo'] ?? '');
            $contrasena = $datos['contrasena'] ?? '';
            $telefono = trim($datos['telefono'] ?? '');


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


            if ($this->repository->crearCliente(compact('CI', 'nombre', 'correo', 'contrasena', 'telefono'))) {
                return ["status" => "success", "message" => "Cuenta creada con éxito. Ya puedes iniciar sesión."];
            }


            throw new Exception("Error interno al crear la cuenta.");
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }


    /**
     * Procesa el intento de inicio de sesión (Orientado a Objetos)
     */
    public function login(array $datos): array {
        try {
            $correo = trim($datos['correo'] ?? '');
            $contrasenaIngresada = $datos['contrasena'] ?? '';


            if (empty($correo) || empty($contrasenaIngresada)) {
                throw new Exception("El correo y la contraseña son obligatorios.");
            }


            $usuario = $this->repository->buscarPorCorreo($correo);


            if (!$usuario) {
                throw new Exception("Credenciales incorrectas.");
            }


            $passValida = password_verify($contrasenaIngresada, $usuario->getContrasena())
                || $contrasenaIngresada === $usuario->getContrasena();


            if (!$passValida) {
                throw new Exception("Credenciales incorrectas.");
            }


            // Iniciar sesión si no está activa
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }


            // 🔐 Regenerar ID de sesión para prevenir Session Fixation
            session_regenerate_id(true);


            // Asignar variables de sesión
            $_SESSION['CI'] = $usuario->getCI();
            $_SESSION['nombre'] = $usuario->getNombre();
            $_SESSION['rol'] = $usuario->getRol();


            return [
                "status" => "success",
                "rol" => $usuario->getRol(),
                "message" => "Bienvenido " . $usuario->getNombre()
            ];
        } catch (Exception $e) {
            return ["status" => "error", "message" => $e->getMessage()];
        }
    }


    public function logout(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }


        // Limpiar sesión completamente
        $_SESSION = [];
        session_destroy();


        header("Location: index.php");
        exit();
    }
}
