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

        // SI ESTÁ AUTENTICADO, REDIRIGE SEGÚN SU ROL
        return match ($request->user()->role->name) {
            'admin'  => redirect()->route('admin.dashboard'),
            'coach'  => redirect()->route('coach.classes.index'),
            'player' => redirect()->route('player.reservations.index'),
            default  => redirect()->route('login'),
        };
    }
}
