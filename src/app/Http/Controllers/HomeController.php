<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function index(Request $request)
    {
        $user = $request->user()->load('role');
        return view('home', compact('user'));
    }
}
