<?php

namespace App\Http\Controllers\Coach;

use App\Http\Controllers\Controller;
use App\Models\ClassRegistration;
use App\Models\Court;
use App\Models\PadelClass;
use App\Models\User;
use App\Notifications\ClassRegistrationNotification;
use App\Notifications\PublicClassNotification;
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

        $slots         = collect();
        $selectedCourt = null;
        $courtId       = null;
        $date          = null;

        if ($request->filled('court_id') && $request->filled('date')) {

            $selectedCourt = Court::find($request->court_id);
            $date          = $request->date;
            $courtId       = $request->court_id;
            $duration      = 90;

            // GENERAMOS TODAS LAS FRANJAS DE 09:00 A 20:30 (LA ÚLTIMA TERMINARÁ A 22:00)
            $allHours = [];
            $start = \Carbon\Carbon::createFromTime(9, 0);
            $end   = \Carbon\Carbon::createFromTime(20, 30);

            while ($start->lte($end)) {
                $allHours[] = $start->format('H:i');
                $start->addMinutes(30);
            }

            // FILTRAMOS LAS OCUPADAS POR RESERVAS O CLASES
            $slots = collect($allHours)->filter(function ($slot) use ($date, $courtId, $duration) {
                $slotStart = \Carbon\Carbon::createFromFormat('H:i', $slot);
                $slotEnd   = $slotStart->copy()->addMinutes($duration);

                $reservationOverlap = \App\Models\Reservation::where('court_id', $courtId)
                    ->where('reservation_date', $date)
                    ->where('status', '!=', 'cancelled')
                    ->where(function ($q) use ($slotStart, $slotEnd) {
                        $q->where('start_time', '<', $slotEnd->format('H:i:s'))
                            ->where('end_time', '>', $slotStart->format('H:i:s'));
                    })->exists();

                $classOverlap = PadelClass::where('court_id', $courtId)
                    ->where('date', $date)
                    ->where('status', '!=', 'cancelled')
                    ->where(function ($q) use ($slotStart, $slotEnd) {
                        $q->where('start_time', '<', $slotEnd->format('H:i:s'))
                            ->where('end_time', '>', $slotStart->format('H:i:s'));
                    })->exists();

                return !$reservationOverlap && !$classOverlap;
            })->values();
        }

        return view('coach.classes.create', compact('courts', 'players', 'slots', 'selectedCourt', 'courtId', 'date'));
    }

    /**
     * GUARDAR LA CLASE
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:50',
            'court_id'    => 'required|exists:courts,id',
            'type' => 'required|in:individual,group',
            'level'       => 'required|in:initiation,intermediate,advanced',
            'visibility'  => 'required|in:public,private',
            'date'        => 'required|date|after_or_equal:today',
            'start_time'  => 'required|date_format:H:i',
            'max_players' => 'nullable|integer|min:1|max:4',
            'price'       => 'required|numeric|min:0',
            'players'     => 'nullable|array',
            'players.*'   => 'exists:users,id',
        ]);

        // CALCULAMOS EL END_TIME AUTOMÁTICAMENTE
        $endTime = \Carbon\Carbon::createFromFormat('H:i', $validated['start_time'])
            ->addMinutes(90)
            ->format('H:i');

        // SOLAPAMIENTO CON OTRAS CLASES
        $classOverlap = PadelClass::where('court_id', $validated['court_id'])
            ->where('date', $validated['date'])
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($validated, $endTime) {
                $q->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $validated['start_time']);
            })->exists();

        if ($classOverlap) {
            return back()->withErrors([
                'start_time' => 'Ya existe una clase en esa pista para ese horario.'
            ])->withInput();
        }

        // SOLAPAMIENTO CON RESERVAS DE JUGADORES
        $reservationOverlap = \App\Models\Reservation::where('court_id', $validated['court_id'])
            ->where('reservation_date', $validated['date'])
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($validated, $endTime) {
                $q->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $validated['start_time']);
            })->exists();

        if ($reservationOverlap) {
            return back()->withErrors([
                'start_time' => 'Ya existe una reserva de jugador en esa pista para ese horario.'
            ])->withInput();
        }

        // SI ES INDIVIDUAL, FORZAMOS MAX_PLAYERS A 1
        if ($validated['type'] === 'individual') {
            $validated['max_players'] = 1;
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
            'end_time'    => $endTime,
            'max_players' => $validated['max_players'],
            'price'       => $validated['price'],
        ]);

        // SI LA CLASE ES PÚBLICA, NOTIFICAR A TODOS LOS JUGADORES
        if ($validated['visibility'] === 'public') {
            $players = User::whereHas('role', fn($q) => $q->where('name', 'player'))->get();
            foreach ($players as $player) {
                $player->notify(new PublicClassNotification($padelClass));
            }
        }

        // SI LA CLASE ES PRIVADA, INSCRIBIR Y NOTIFICAR A LOS ALUMNOS
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

        $slots         = collect();
        $selectedCourt = null;

        // USAMOS LA PISTA Y FECHA DE LA CLASE POR DEFECTO SI NO SE HA SELECCIONADO OTRA
        $courtId = $request->filled('court_id') ? $request->court_id : $class->court_id;
        $date    = $request->filled('date')     ? $request->date     : $class->date;

        $selectedCourt = Court::find($courtId);

        $allSlots = [];
        $start = \Carbon\Carbon::createFromTime(9, 0);
        $end   = \Carbon\Carbon::createFromTime(20, 30);

        while ($start->lte($end)) {
            $allSlots[] = $start->format('H:i');
            $start->addMinutes(30);
        }

        $slots = collect($allSlots)->filter(function ($slot) use ($date, $courtId, $class) {
            $slotStart = \Carbon\Carbon::createFromFormat('H:i', $slot);
            $slotEnd   = $slotStart->copy()->addMinutes(90);

            $reservationOverlap = \App\Models\Reservation::where('court_id', $courtId)
                ->where('reservation_date', $date)
                ->where('status', '!=', 'cancelled')
                ->where(function ($q) use ($slotStart, $slotEnd) {
                    $q->where('start_time', '<', $slotEnd->format('H:i:s'))
                        ->where('end_time', '>', $slotStart->format('H:i:s'));
                })->exists();

            $classOverlap = PadelClass::where('court_id', $courtId)
                ->where('date', $date)
                ->where('status', '!=', 'cancelled')
                ->where('id', '!=', $class->id) // EXCLUIMOS LA PROPIA CLASE
                ->where(function ($q) use ($slotStart, $slotEnd) {
                    $q->where('start_time', '<', $slotEnd->format('H:i:s'))
                        ->where('end_time', '>', $slotStart->format('H:i:s'));
                })->exists();

            return !$reservationOverlap && !$classOverlap;
        })->values();

        return view('coach.classes.edit', compact('class', 'courts', 'players', 'enrolledIds', 'slots', 'selectedCourt', 'courtId', 'date'));
    }

    /**
     * ACTUALIZAR LA CLASE
     */
    public function update(Request $request, PadelClass $class)
    {
        $user = $request->user();

        if ($class->coach_id !== $request->user()->id && $user->role->name !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:50',
            'court_id'    => 'required|exists:courts,id',
            'type'        => 'required|in:individual,group',
            'level'       => 'required|in:initiation,intermediate,advanced',
            'visibility'  => 'prohibited',
            'date'        => 'required|date',
            'start_time'  => 'required|date_format:H:i',
            'max_players' => 'required|integer|min:1|max:4',
            'price'       => 'required|numeric|min:0',
            'status'      => 'required|in:registered,cancelled,completed',
        ]);

        // CALCULAMOS EL END_TIME AUTOMÁTICAMENTE
        $endTime = \Carbon\Carbon::createFromFormat('H:i', $validated['start_time'])
            ->addMinutes(90)
            ->format('H:i');

        // SOLAPAMIENTO CON OTRAS CLASES (EXCLUYENDO LA PROPIA)
        $classOverlap = PadelClass::where('court_id', $validated['court_id'])
            ->where('date', $validated['date'])
            ->where('status', '!=', 'cancelled')
            ->where('id', '!=', $class->id)
            ->where(function ($q) use ($validated, $endTime) {
                $q->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $validated['start_time']);
            })->exists();

        if ($classOverlap) {
            return back()->withErrors([
                'start_time' => 'Ya existe una clase en esa pista para ese horario.'
            ])->withInput();
        }

        // SOLAPAMIENTO CON RESERVAS DE JUGADORES
        $reservationOverlap = \App\Models\Reservation::where('court_id', $validated['court_id'])
            ->where('reservation_date', $validated['date'])
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($validated, $endTime) {
                $q->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $validated['start_time']);
            })->exists();

        if ($reservationOverlap) {
            return back()->withErrors([
                'start_time' => 'Ya existe una reserva de jugador en esa pista para ese horario.'
            ])->withInput();
        }

        // SI ES INDIVIDUAL, FORZAMOS MAX_PLAYERS A 1
        if ($validated['type'] === 'individual') {
            $validated['max_players'] = 1;
        }

        // MANTENEMOS LA VISIBILIDAD ORIGINAL
        $validated['visibility'] = $class->visibility;

        $class->update(array_merge($validated, ['end_time' => $endTime]));

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
