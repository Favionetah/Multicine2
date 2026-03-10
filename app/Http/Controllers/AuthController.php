<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Maneja el inicio de sesión para todos los roles.
     * - Si la petición quiere JSON (Postman/API) → autentica con JWT y retorna token.
     * - Si es una petición web normal → usa Auth::attempt() con sesiones para Blade.
     */
    public function login(Request $request)
    {
        // 1. Buscamos al usuario uniendo con la tabla roles
        $usuario = Usuario::join('roles', 'usuarios.idRol', '=', 'roles.idRol')
                    ->where('usuarios.correo', $request->correo)
                    ->where('usuarios.estado', 'activo')
                    ->select('usuarios.*', 'roles.nombre as rol_nombre')
                    ->first();

        // 2. Verificación de contraseña (Texto plano o Hash)
        $passValida = false;
        if ($usuario) {
            if ($request->contrasena === $usuario->contrasena || Hash::check($request->contrasena, $usuario->contrasena)) {
                $passValida = true;
            }
        }

        if (!$usuario || !$passValida) {
            // Si es API devuelve JSON, si es web redirige con error
            if ($request->wantsJson()) {
                return response()->json(['status' => 'error', 'message' => 'Credenciales incorrectas.'], 401);
            }
            return back()->withErrors(['correo' => 'Credenciales incorrectas.'])->withInput();
        }

        // -------------------------------------------------------
        // INICIO DE SESIÓN DUAL (Web + API)
        // -------------------------------------------------------
        
        // 1. Iniciamos sesión en el guard web (sesiones)
        Auth::guard('web')->login($usuario);

        // SOLO si la petición tiene soporte de sesiones (middleware web activo)
        // evitamos el error RuntimeException: Session store not set on request.
        if ($request->hasSession()) {
            $request->session()->regenerate();

            // Guardamos datos extra en la sesión
            session([
                'CI'     => $usuario->CI,
                'nombre' => $usuario->nombre,
                'rol'    => strtolower($usuario->rol_nombre),
            ]);
        }

        // -------------------------------------------------------
        // REGISTRO DE ACTIVIDAD (LOG)
        // -------------------------------------------------------
        if (in_array($usuario->idRol, [1, 2])) {
            \App\Models\Log::create([
                'fecha'  => now(),
                'nombre' => $usuario->nombre,
                'rol'    => strtolower($usuario->rol_nombre),
                'accion' => 'Inició sesión en el sistema.'
            ]);
        }

        // 2. Si la petición es JSON, generamos y devolvemos el token JWT
        if ($request->wantsJson()) {
            $token = JWTAuth::fromUser($usuario);


            return response()->json([
                'status'       => 'success',
                'message'      => 'Bienvenido ' . $usuario->nombre,
                'rol'          => strtolower($usuario->rol_nombre),
                'access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => config('jwt.ttl', 1440) * 60,
            ]);
        }

        // 3. Respuesta para peticiones síncronas (si las hubiera)
        return response()->json([
            'status'  => 'success',
            'rol'     => strtolower($usuario->rol_nombre),
            'message' => 'Bienvenido ' . $usuario->nombre,
        ]);
    }


    /**
     * Maneja el registro de nuevos clientes.
     */
    public function registrar(Request $request)
    {
        try {
            DB::table('usuarios')->insert([
                'CI'        => $request->CI,
                'nombre'    => $request->nombre,
                'correo'    => $request->correo,
                'contrasena'=> $request->contrasena,
                'telefono'  => $request->telefono,
                'idRol'     => 3,
                'estado'    => 'activo',
                'puntos'    => 0,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => '¡Cuenta creada! Ya puedes iniciar sesión.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'El CI o correo ya están registrados.',
            ]);
        }
    }

    /**
     * Cierra la sesión del guard web (vistas Blade).
     */
    public function logout(Request $request)
    {
        $usr = Auth::guard('web')->user();
        if ($usr) {
           \App\Models\Log::create([
               'fecha'  => now(),
               'nombre' => $usr->nombre,
               'rol'    => strtolower(session('rol', 'cajero')),
               'accion' => 'Cerró sesión (Web).'
           ]);
        }
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    /**
     * Cierra la sesión del guard api (JWT).
     * Invalida el token actual.
     */
    public function logoutApi(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['status' => 'success', 'message' => 'Sesión API cerrada correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'No se pudo cerrar la sesión.'], 500);
        }
    }

    /**
     * Retorna la información del usuario autenticado vía JWT.
     */
    public function me()
    {
        return response()->json(Auth::guard('api')->user());
    }
}