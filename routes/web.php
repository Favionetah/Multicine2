<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;

// ─────────────────────────────────────────────────────────────
// RUTAS PÚBLICAS (sin autenticación)
// ─────────────────────────────────────────────────────────────

// Página principal / Login
Route::get('/', function () {
    return view('index');
})->name('login');

// Procesar login (detecta automáticamente si es web o JSON)
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Registro de clientes
Route::post('/registro', [AuthController::class, 'registrar'])->name('registro');

// ─────────────────────────────────────────────────────────────
// RUTAS PROTEGIDAS — GUARD WEB (sesiones + Blade)
// ─────────────────────────────────────────────────────────────

// Rutas del Cliente
Route::middleware('auth:web')->group(function () {
    Route::get('/cartelera', fn() => view('cartelera_cliente'))->name('cartelera');
    Route::get('/historial',  fn() => view('historial_cliente'))->name('historial');
    Route::get('/catalogo',   fn() => view('catalogo_cliente'))->name('catalogo');
});

// Rutas del Cajero
Route::get('/cajero', fn() => view('cajero'))
    ->middleware('auth:web')
    ->name('cajero');

// Rutas del Administrador
Route::middleware('auth:web')->group(function () {
    Route::get('/admin', function () {
        if (session('rol') !== 'administrador') {
            return redirect('/');
        }
        return view('admin');
    })->name('admin');

    Route::get('/admin/peliculas',  fn() => view('admin_peliculas_vue'))->name('admin.peliculas');
    Route::get('/admin/salas',      fn() => view('admin_salas'))->name('admin.salas');
    Route::get('/admin/funciones',  fn() => view('admin_funciones'))->name('admin.funciones');
    Route::get('/admin/reportes',   fn() => view('admin_reportes'))->name('admin.reportes');
    Route::get('/admin/tarifas',    fn() => view('admin_tarifas'))->name('admin.tarifas');
    Route::get('/admin/empleados',  fn() => view('admin_empleados'))->name('admin.empleados');
});

// ─────────────────────────────────────────────────────────────
// LOGOUT (guard web)
// ─────────────────────────────────────────────────────────────
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');