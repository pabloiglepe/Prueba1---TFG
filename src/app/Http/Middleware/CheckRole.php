<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{    
    /**
     * FUNCION QUE MANEJA EL ROL DEL USUARIO AUTENTICADO, Y SI DICHO USUARIO ESTÁ AUTENTICADO
     *
     * @param  Request $request
     * @param  Closure $next
     * @param  String $roles
     * @return Response
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // SI EL USUARIO NO ESTÁ AUTENTICADO LO ENVIA AL LOGIN
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // SI EL ROL DEL USUARIO NO SE ENCUENTRA ENTRE LOS PERMITIDOS SALTA ERROR 403
        if (!in_array($request->user()->role->name, $roles)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}