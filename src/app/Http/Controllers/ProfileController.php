<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\PadelClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // VISTA DEL PERFIL
    public function index(Request $request)
    {
        $user = $request->user()->load(['role', 'classesByCoach.registered', 'classesByCoach.court']);

        // HISTORIAL DE RESERVAS
        $reservations = Reservation::where('user_id', $user->id)
            ->with('court')
            ->orderBy('reservation_date', 'desc')
            ->get();

        // GASTO TOTAL EN RESERVAS
        $totalSpentReservations = $reservations
            ->where('status', '!=', 'cancelled')
            ->sum('total_price');

        // CLASES INSCRITAS
        $classes = PadelClass::whereHas(
            'registered',
            fn($q) =>
            $q->where('user_id', $user->id)->where('status', 'registered')
        )
            ->with(['coach', 'court'])
            ->orderBy('date', 'desc')
            ->get();

        // GASTO TOTAL EN CLASES
        $totalSpentClasses = $classes->sum('price');

        // GASTO TOTAL
        $totalSpent = $totalSpentReservations + $totalSpentClasses;

        // DATOS ESPECÍFICOS PARA EL ENTRENADOR
        $coachStats = null;
        if ($user->role->name === 'coach') {
            $coachStats = [
                'total_classes'  => $user->classesByCoach()->count(),
                'total_students' => $user->classesByCoach()
                    ->withCount([
                        'registered as students_count' => fn($q) =>
                        $q->where('status', 'registered')
                    ])
                    ->get()
                    ->sum('students_count'),
                'total_revenue'  => $user->classesByCoach()
                    ->with(['registered' => fn($q) => $q->where('status', 'registered')])
                    ->get()
                    ->sum(fn($class) => $class->registered->count() * $class->price),
            ];
        }

        return view('profile', compact(
            'user',
            'reservations',
            'totalSpentReservations',
            'classes',
            'totalSpentClasses',
            'totalSpent',
            'coachStats'
        ));
    }

    // ACTUALIZAR DATOS PERSONALES
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'         => 'required|string|max:50',
            'phone_number' => 'required|string|size:9|unique:users,phone_number,' . $user->id,
        ]);

        $user->update($validated);

        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    // CAMBIAR CONTRASEÑA
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, $request->user()->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.']);
        }

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }

    // BORRADO LÓGICO DE CUENTA (RGPD)
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => 'required',
        ]);

        if (!Hash::check($request->password, $request->user()->password)) {
            return back()->withErrors(['password' => 'La contraseña no es correcta.']);
        }

        $user = $request->user();

        // CANCELAR RESERVAS PENDIENTES
        Reservation::where('user_id', $user->id)
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);

        // BORRADO LÓGICO
        auth()->logout();
        $user->delete();

        return redirect('/')->with('success', 'Tu cuenta ha sido eliminada correctamente.');
    }


    // EXPORTAR DATOS DEL PERFIL (RGPD)
    public function export(Request $request)
    {
        $user = $request->user();

        $data = [
            'datos_personales' => [
                'nombre'       => $user->name,
                'email'        => $user->email,
                'telefono'     => $user->phone_number,
                'rol'          => $user->role->name,
                'registro'     => $user->created_at->format('d/m/Y H:i'),
                'rgpd_consent' => $user->rgpd_consent ? 'Aceptado' : 'No aceptado',
            ],
            'reservas' => Reservation::where('user_id', $user->id)
                ->with('court')
                ->get()
                ->map(fn($r) => [
                    'pista'   => $r->court->name,
                    'fecha'   => $r->reservation_date,
                    'inicio'  => $r->start_time,
                    'fin'     => $r->end_time,
                    'precio'  => $r->total_price . '€',
                    'estado'  => $r->status,
                ]),
            'clases' => PadelClass::whereHas(
                'registered',
                fn($q) =>
                $q->where('user_id', $user->id)->where('status', 'registered')
            )
                ->with(['coach', 'court'])
                ->get()
                ->map(fn($c) => [
                    'titulo'      => $c->title,
                    'fecha'       => $c->date,
                    'entrenador'  => $c->coach->name,
                    'pista'       => $c->court->name,
                    'precio'      => $c->price . '€',
                ]),
        ];

        $filename = 'mis-datos-padelsync-' . now()->format('Y-m-d') . '.json';

        return response()->json($data, 200, [
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
