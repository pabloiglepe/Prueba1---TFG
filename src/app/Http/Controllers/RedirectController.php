<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RedirectController extends Controller
{
    public function home(Request $request)
    {
        // SI NO ESTÁ AUTENTICADO, MUESTRA LA BIENVENIDA
        if (!$request->user()) {
            return view('welcome');
        }

        // SI ESTÁ AUTENTICADO, REDIRIGE SIEMPRE A LA HOME CON CARRUSEL
        return redirect()->route('home');
    }
}
