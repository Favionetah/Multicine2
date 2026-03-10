<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use PHPOpenSourceSaver\JWTAuth\Http\Middleware\Authenticate as JwtAuthenticate;
use PHPOpenSourceSaver\JWTAuth\Http\Middleware\RefreshToken as JwtRefresh;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 1. Excluir rutas API del CSRF
        //    El panel admin envía X-CSRF-TOKEN en el header, pero las rutas
        //    api/* no tienen VerifyCsrfToken por defecto → mantener excluidas.
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);

        // 2. Registrar alias del middleware JWT
        $middleware->alias([
            'jwt.auth'    => JwtAuthenticate::class,
            'jwt.refresh' => JwtRefresh::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();


