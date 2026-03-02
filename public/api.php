<?php

declare(strict_types=1);

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', '0');

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    echo json_encode(["status" => "error", "message" => "Error de PHP: $errstr en la línea $errline de $errfile"]);
    exit;
});
set_exception_handler(function($e) {
    echo json_encode(["status" => "error", "message" => "Fallo de PHP: " . $e->getMessage() . " en la línea " . $e->getLine()]);
    exit;
});


require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Repositories/UsuarioRepository.php';
require_once __DIR__ . '/../src/Controllers/UsuarioController.php';


use Config\Database;
use App\Repositories\UsuarioRepository;
use App\Controllers\UsuarioController;
use App\Repositories\PeliculaRepository;
use App\Controllers\PeliculaController;
use App\Repositories\FuncionRepository; 
use App\Controllers\FuncionController;  
use App\Repositories\SalaRepository;
use App\Controllers\SalaController;

try {
    $db = (new Database())->connect();

    // Instanciar dependencias
    $peliculaCtrl = new PeliculaController(new PeliculaRepository($db));
    $usuarioCtrl = new UsuarioController(new UsuarioRepository($db));
    $funcionCtrl = new FuncionController(new FuncionRepository($db));
    $salaCtrl = new SalaController(new SalaRepository($db));
    

    $accion = $_GET['action'] ?? $_POST['action'] ?? null;

    // Leer JSON si es necesario
    $inputJSON = json_decode(file_get_contents('php://input'), true);
    if (!$accion && isset($inputJSON['action'])) {
        $accion = $inputJSON['action'];
    }

    $respuesta = [];

    switch ($accion) {
        case 'login':
            $respuesta = $usuarioCtrl->login($_POST);
            break;

        case 'registro_cliente':
            $respuesta = $usuarioCtrl->registrarCliente($_POST);
            break;

        case 'listar_peliculas':
            $respuesta = $peliculaCtrl->listarPeliculas();
            break;
        
        case 'editar_pelicula':
            $respuesta = $peliculaCtrl->editarPelicula($_POST, $_FILES);
            break;

        case 'crear_pelicula':
            $respuesta = $peliculaCtrl->crearPelicula($_POST, $_FILES);
            break;

        case 'eliminar_pelicula':
            $id = (int)($_POST['id'] ?? $inputJSON['id'] ?? 0);
            $respuesta = $peliculaCtrl->eliminarPelicula($id);
            break;
        
        case 'crear_funcion':
            $respuesta = $funcionCtrl->crearFuncion($_POST);
            break;
        
        case 'listar_funciones':
            $respuesta = $funcionCtrl->listarFunciones();
            break;

        case 'editar_funcion':
            $respuesta = $funcionCtrl->editarFuncion($_POST);
            break;

        case 'eliminar_funcion':
            $id = (int)($_POST['id'] ?? $inputJSON['id'] ?? 0);
            $respuesta = $funcionCtrl->eliminarFuncion($id);
            break;

        case 'listar_salas':
            $respuesta = $salaCtrl->listarSalas();
            break;

        case 'crear_sala':
            $respuesta = $salaCtrl->crearSala($_POST, $_FILES);
            break;
            
        case 'editar_sala':
            $respuesta = $salaCtrl->editarSala($_POST, $_FILES);
            break;

        case 'eliminar_sala':
            $id = (int)($_POST['id'] ?? $inputJSON['id'] ?? 0);
            $respuesta = $salaCtrl->eliminarSala($id);
            break;
        
        case 'cartelera_cajero':
            $respuesta = $funcionCtrl->carteleraCajero();
            break; 

        case 'vender_boletos':
            $respuesta = $funcionCtrl->venderBoletos($_POST);
            break;
            
        // --- NUEVAS ACCIONES DEL CLIENTE AÑADIDAS ---
        case 'comprar_cliente':
            $respuesta = $funcionCtrl->comprarBoletosCliente($_POST);
            break;

        case 'historial_cliente':
            $respuesta = $funcionCtrl->historialCliente();
            break;
        // ---------------------------------------------

        case 'logout':
            if (session_status() === PHP_SESSION_NONE) session_start();
            session_destroy();
            header("Location: index.php");
            exit();

        default:
            throw new Exception("Acción '$accion' no permitida.");
    }

    echo json_encode($respuesta);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}