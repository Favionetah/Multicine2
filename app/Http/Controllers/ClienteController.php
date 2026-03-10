<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    public function verificarSocio(Request $request) {
    $ci = $request->input('ci');
    // Usamos 'usuarios' y 'CI' tal cual están en tu phpMyAdmin
    $cliente = DB::table('usuarios')->where('CI', $ci)->first(); 

    if ($cliente) {
        return response()->json([
            'status' => 'success',
            'nombre' => $cliente->nombre,
            'puntos' => $cliente->puntos ?? 0
        ]);
    }
    return response()->json(['status' => 'error', 'message' => 'No encontrado']);
}
}