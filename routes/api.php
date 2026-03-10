<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PeliculaController;
use App\Http\Controllers\SalaController;
use App\Http\Controllers\FuncionController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\CarteleraController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\VentaController;

// ─────────────────────────────────────────────────────────────
// 1. RUTAS DE AUTENTICACIÓN API (públicas y protegidas)
//    Nota: Agregamos el middleware 'web' para que la sesión esté
//    disponible y el AuthController no falle al intentar regenerar sesión.
// ─────────────────────────────────────────────────────────────
Route::middleware(['web'])->group(function () {
    // POST /api/login  → devuelve Bearer Token JWT (y opcionalmente inicia sesión web)
    Route::post('/login',  [AuthController::class, 'login']);
    
    // Rutas protegidas por JWT (que también necesitan iniciar el driver de sesión web por seguridad)
    Route::post('/logout', [AuthController::class, 'logoutApi'])->middleware('auth:api');
    Route::get('/me',      [AuthController::class, 'me'])->middleware('auth:api');
});


// ─────────────────────────────────────────────────────────────
// 2. RUTAS PÚBLICAS — sin autenticación requerida
// ─────────────────────────────────────────────────────────────
Route::get('/peliculas',              [PeliculaController::class,      'index']);
Route::get('/funciones',              [FuncionController::class,       'index']);
Route::get('/salas',                  [SalaController::class,          'index']);
Route::get('/empleados/datos',        [EmpleadoController::class,      'getDatos']);
Route::get('/config/tarifas',         [ConfiguracionController::class, 'getTarifas']);
Route::get('/tarifas',                [CarteleraController::class,     'getTarifas']);
Route::get('/puntos/cliente/{ci}',    [ClienteController::class,       'obtenerPuntos']);
Route::get('/historial/{ci}',         [HistorialController::class,     'getHistorial']);
Route::get('/cartelera',              [CarteleraController::class,     'getCartelera']);

Route::post('/socio/verificar',       [ClienteController::class,  'verificarSocio']);
Route::post('/venta/procesar',        [VentaController::class,    'procesarVenta']);
Route::post('/ticket/verificar',      [VentaController::class,    'verificarTicket']);

// ─────────────────────────────────────────────────────────────
// 3. RUTAS PROTEGIDAS — Acepta AMBOS flujos simultáneamente:
//    - Web (panel admin Blade): autenticado por sesión ('web').
//    - API (Postman/cliente):   autenticado por JWT ('api').
//
//    Nota: Agregamos el middleware 'web' al grupo para iniciar la sesión
//    en las rutas /api/* y permitir que auth:web funcione.
//    La protección CSRF se omite porque la configuramos en bootstrap/app.php.
// ─────────────────────────────────────────────────────────────
Route::middleware(['web', 'auth:web,api'])->group(function () {


    // ── Gestión de Películas ──────────────────────────────────
    Route::post('/peliculas',              [PeliculaController::class, 'store']);
    Route::post('/peliculas/eliminar',     [PeliculaController::class, 'eliminar']);

    // ── Gestión de Funciones ──────────────────────────────────
    Route::post('/funciones',              [FuncionController::class, 'store']);
    Route::post('/funciones/editar',       [FuncionController::class, 'editar']);
    Route::post('/funciones/eliminar',     [FuncionController::class, 'eliminar']);

    // ── Gestión de Salas ──────────────────────────────────────
    Route::get('/salas/{id}',              [SalaController::class, 'show']);
    Route::post('/salas',                  [SalaController::class, 'store']);
    Route::match(['put', 'patch'], '/salas/{id}', [SalaController::class, 'update']);
    Route::delete('/salas/{id}',           [SalaController::class, 'destroy']);
    Route::post('/salas/eliminar',         [SalaController::class, 'eliminar']);

    // ── Gestión de Empleados ──────────────────────────────────
    Route::post('/empleados/gestionar',    [EmpleadoController::class, 'gestionar']);

    // ── Configuración y Reportes ──────────────────────────────
    Route::post('/config/tarifas',         [ConfiguracionController::class, 'updateTarifas']);
    Route::post('/reportes/generar',       [ReporteController::class,       'generar']);
});