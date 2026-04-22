<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // PARA NO DAR PISTAS EN LOS ERRORES SOBRE SI UNA RUTA PUEDA O NO EXISTIR, CONVIERTO CUALQUIER ERROR 403 (Forbidden) EN 404 (Not Found) 
        // $exceptions->render(function (AccessDeniedHttpException $e, $request) {
        //     abort(404);
        // });
    })->create();
