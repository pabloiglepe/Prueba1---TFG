<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Models\ClassRegistration;
use App\Models\PadelClass;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    // VISTA PRINCIPAL DE CLASES DEL JUGADOR
    public function index(Request $request)
    {
        $user = $request->user();

        // CLASES EN LAS QUE YA ESTÁ INSCRITO
        $myClasses = PadelClass::whereHas(
            'registered',
            fn($q) =>
            $q->where('user_id', $user->id)->where('status', 'registered')
        )
            ->with(['coach', 'court'])
            ->where('status', '!=', 'cancelled')
            ->orderBy('date')
            ->get();


        // CLASES PÚBLICAS DISPONIBLES (no inscrito, con plazas, futuras)
        $availableClasses = PadelClass::where('visibility', 'public')
            ->where('status', 'registered')
            ->where('date', '>=', today())
            ->whereDoesntHave(
                'registered',
                fn($q) =>
                $q->where('user_id', $user->id)->where('status', 'registered')
            )
            ->with(['coach', 'court'])
            ->withCount([
                'registered as enrolled_count' => fn($q) =>
                $q->where('status', 'registered')
            ])
            ->having('enrolled_count', '<', \Illuminate\Support\Facades\DB::raw('max_players'))
            ->orderBy('date')
            ->get()
            ->filter(fn($class) => !$class->isFull());

        return view('player.classes.index', compact('myClasses', 'availableClasses'));
    }


    // INSCRIBIRSE A UNA CLASE PÚBLICA
    public function register(Request $request, PadelClass $class)
    {
        $user = $request->user();

        // COMPROBAMOS SI YA EXISTE UNA INSCRIPCIÓN (ACTIVA O CANCELADA)
        $existing = ClassRegistration::where('class_id', $class->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            if ($existing->status === 'registered') {
                return back()->with('error', 'Ya estás inscrito en esta clase.');
            }
            // SI ESTABA CANCELADA LA REACTIVAMOS
            $existing->update(['status' => 'registered']);
        } else {
            // SI NO EXISTE LA CREAMOS
            ClassRegistration::create([
                'class_id' => $class->id,
                'user_id'  => $user->id,
                'status'   => 'registered',
            ]);
        }

        return back()->with('success', 'Inscripción realizada correctamente.');
    }


    // CANCELAR INSCRIPCIÓN
    public function cancel(Request $request, PadelClass $class)
    {
        ClassRegistration::where('class_id', $class->id)
            ->where('user_id', $request->user()->id)
            ->update(['status' => 'cancelled']);

        return back()->with('success', 'Inscripción cancelada correctamente.');
    }
}
