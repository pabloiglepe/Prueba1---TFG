<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\ClassRegistration;
use App\Models\Court;
use App\Models\PadelClass;
use App\Models\User;
use App\Notifications\ClassRegistrationNotification;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    /**
     * LISTADO DE CLASES DEL ENTRENADOR
     */
    public function index(Request $request)
    {
        $classes = PadelClass::where('coach_id', $request->user()->id)
            ->with(['court', 'registered'])
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        return view('coach.classes.index', compact('classes'));
    }

    /**
     * FORMULARIO DE CREACIÓN DE UNA CLASE
     */
    public function create(Request $request)
    {
        $courts  = Court::where('is_active', true)->get();
        $players = User::whereHas('role', fn($q) => $q->where('name', 'player'))->get();

        return view('coach.classes.create', compact('courts', 'players'));
    }

    /**
     * GUARDAR LA CLASE
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:50',
            'court_id'    => 'required|exists:courts,id',
            'type'        => 'required|in:individual,grupal',
            'level'       => 'required|in:initiation,intermediate,advanced',
            'visibility'  => 'required|in:public,private',
            'date'        => 'required|date|after_or_equal:today',
            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'required|date_format:H:i|after:start_time',
            'max_players' => 'required|integer|min:1|max:4',
            'price'       => 'required|numeric|min:0',
            'players'     => 'nullable|array',
            'players.*'   => 'exists:users,id',
        ]);


        // COMPROBAMOS QUE LA PISTA NO ESTÉ YA OCUPADA
        $overlap = PadelClass::where('court_id', $validated['court_id'])
            ->where('date', $validated['date'])
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($validated) {
                $q->where('start_time', '<', $validated['end_time'])
                    ->where('end_time', '>', $validated['start_time']);
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors([
                'court_id' => 'La pista ya está ocupada en ese horario.'
            ])->withInput();
        }


        $padelClass = PadelClass::create([
            'coach_id'    => $request->user()->id,
            'court_id'    => $validated['court_id'],
            'title'       => $validated['title'],
            'type'        => $validated['type'],
            'level'       => $validated['level'],
            'visibility'  => $validated['visibility'],
            'status'      => 'registered',
            'date'        => $validated['date'],
            'start_time'  => $validated['start_time'],
            'end_time'    => $validated['end_time'],
            'max_players' => $validated['max_players'],
            'price'       => $validated['price'],
        ]);

        // SI LA CLASE ES PRIVADA, INSCRIBIR Y NOTIFICAR A LOS ALUMNOS SELECCIONADOS
        if ($validated['visibility'] === 'private' && !empty($validated['players'])) {
            foreach ($validated['players'] as $playerId) {
                ClassRegistration::create([
                    'class_id' => $padelClass->id,
                    'user_id'  => $playerId,
                    'status'   => 'registered',
                ]);

                $player = User::find($playerId);
                $player->notify(new ClassRegistrationNotification($padelClass));
            }
        }

        return redirect()->route('coach.classes.index')
            ->with('success', 'Clase creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * FORMULARIO DE EDICIÓN DE LA CLASE
     */
    public function edit(Request $request, PadelClass $class)
    {
        $user = $request->user();

        if ($class->coach_id !== $user->id && $user->role->name !== 'admin') {
            abort(403);
        }

        $courts      = Court::where('is_active', true)->get();
        $players     = User::whereHas('role', fn($q) => $q->where('name', 'player'))->get();
        $enrolledIds = $class->players->pluck('id')->toArray();

        return view('coach.classes.edit', compact('class', 'courts', 'players', 'enrolledIds'));
    }

    /**
     * ACTUALIZAR LA CLASE
     */
    public function update(Request $request, PadelClass $class)
    {
        $user = $request->user();

        // SOLO EL ENTRENADOR QUE LA CREÓ O EL ADMIN PUEDEN ACTUALIZAR
        if ($class->coach_id !== $request->user()->id && $user->role->name !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:50',
            'court_id'    => 'required|exists:courts,id',
            'type'        => 'required|in:individual,grupal',
            'level'       => 'required|in:initiation,intermediate,advanced',
            'visibility'  => 'required|in:public,private',
            'date'        => 'required|date',
            'start_time'  => 'required|date_format:H:i',
            'end_time'    => 'required|date_format:H:i|after:start_time',
            'max_players' => 'required|integer|min:1|max:4',
            'price'       => 'required|numeric|min:0',
            'status'      => 'required|in:registered,cancelled,completed',
        ]);

        $class->update($validated);

        return redirect()->route('coach.classes.index')
            ->with('success', 'Clase actualizada correctamente.');
    }

    /**
     * CANCELAR LA CLASE
     */
    public function destroy(Request $request, PadelClass $class)
    {
        $user = $request->user();

        // SOLO EL ENTRENADOR QUE LA CREÓ O EL ADMIN PUEDEN CANCELAR
        if ($class->coach_id !== $request->user()->id && $user->role->name !== 'admin') {
            abort(403);
        }

        $class->update(['status' => 'cancelled']);

        return redirect()->route('coach.classes.index')
            ->with('success', 'Clase cancelada correctamente.');
    }
}
