<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Court;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservationController extends Controller
{

    // CONSTANTE QUE DETERMINA LA DURACIÓN FIJA DE UNA RESERVA EN MINUTOS
    const DURATION = 90;

    /**
     * LISTADO DE RESERVA DE JUGADORES
     */
    public function index(Request $request)
    {
        $reservations = Reservation::where('user_id', $request->user()->id)
            ->with('court')
            ->orderBy('reservation_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        return view('player.reservations.index', compact('reservations'));
    }

    /**
     * FORMULARIO DE BÚSQUEDA Y CREACIÓN
     */
    public function create(Request $request)
    {
        $slots  = collect();
        $courts = collect();

        if ($request->filled('date')) {
            $request->validate([
                'date' => 'required|date|after_or_equal:today',
            ]);

            // DEFINICIÓN Y GENERACIÓN DE LAS FRANJAS HORARIAS DISPONIBLES (09:00 - 22:00 CADA 30 MIN)
            $openingTime = Carbon::createFromFormat('H:i', '09:00');
            $closingTime = Carbon::createFromFormat('H:i', '22:00');
            $current     = $openingTime->copy();

            while ($current->copy()->addMinutes(self::DURATION)->lte($closingTime)) {
                $slots->push($current->format('H:i'));
                $current->addMinutes(30);
            }

            // SI LA FECHA ES HOY, FILTRAMOS LAS FRANJAS QUE YA HAN PASADO
            if (Carbon::parse($request->date)->isToday()) {
                $now = Carbon::now();
                $slots = $slots->filter(function ($slot) use ($now) {
                    return Carbon::createFromFormat('H:i', $slot)->gt($now);
                });
            }

            // SI TAMBIÉN HAY FRANJA SELECCIONADA, BUSCAMOS PISTAS LIBRES
            if ($request->filled('start_time')) {
                $request->validate([
                    'start_time' => 'required|date_format:H:i',
                ]);

                $startTime = $request->start_time;
                $endTime   = Carbon::createFromFormat('H:i', $startTime)
                    ->addMinutes(self::DURATION)
                    ->format('H:i');

                $courts = Court::where('is_active', true)
                    ->whereDoesntHave('reservations', function ($query) use ($request, $startTime, $endTime) {
                        $query->where('reservation_date', $request->date)
                            ->where('status', '!=', 'cancelled')
                            ->where(function ($q) use ($startTime, $endTime) {
                                $q->where('start_time', '<', $endTime)
                                    ->where('end_time', '>', $startTime);
                            });
                    })
                    ->get();
            }
        }

        // GUARDO LA HORA A LA QUE EMPIEZA EL ATARDECER PARA PODER MOSTRAR CUANTO SERÁ EL PRECIO DE LA PISTA
        $nightStartTime = $request->filled('date')
            ? $this->getNightStartTime($request->date)->format('H:i')
            : '20:00';

        return view('player.reservations.create', compact('slots', 'courts', 'nightStartTime'));
    }

    /**
     * GUARDAR LA RESERVA
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'court_id'   => 'required|exists:courts,id',
            'date'       => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
        ]);

        $startTime = $validated['start_time'];
        $endTime   = Carbon::createFromFormat('H:i', $startTime)
            ->addMinutes(self::DURATION)
            ->format('H:i');

        // DOBLE COMPROBACIÓN DE SOLAPAMIENTO EN EL SERVIDOR
        $overlap = Reservation::where('court_id', $validated['court_id'])
            ->where('reservation_date', $validated['date'])
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors([
                'court_id' => 'Esta pista ya está reservada en ese horario.'
            ])->withInput();
        }

        Reservation::create([
            'user_id'          => $request->user()->id,
            'court_id'         => $validated['court_id'],
            'reservation_date' => $validated['date'],
            'start_time'       => $startTime,
            'end_time'         => $endTime,
            'total_price' => $this->calculatePrice($startTime, $validated['date']),
            'status'           => 'pending',
        ]);

        return redirect()->route('player.reservations.index')
            ->with('success', 'Reserva creada correctamente.');
    }

    /**
     * ANTIGUO | MÉTODO QUE CALCULA EL PRECIO CON TARIFA NOCTURNA -> SE CONSIDERA NOCTURNA A PARTIR DE LAS 20:00 
     *
     * @param  string $startTime
     * @param  string $endTime
     * @return float
     */
    // private function calculatePrice(string $startTime, string $endTime): float
    // {
    //     $startTimeFormatted = \Carbon\Carbon::createFromFormat('H:i', $startTime);
    //     $endTimeFormatted  = \Carbon\Carbon::createFromFormat('H:i', $endTime);

    //     $nightStart = \Carbon\Carbon::createFromFormat('H:i', '20:00');

    //     $price = 0;

    //     // MIRAMOS MINUTO A MINUTO PARA CALCULAR EL PRECIO EXACTO POR SI LA RESERVA ES MÁS TARDE DE LAS 20:00
    //     $currentTime = $startTimeFormatted->copy();
    //     while ($currentTime < $endTimeFormatted) {
    //         $next = $currentTime->copy()->addMinutes(30);
    //         if ($next > $endTimeFormatted) $next = $endTimeFormatted->copy();

    //         $midpoint = $currentTime->copy()->addMinutes(
    //             $currentTime->diffInMinutes($next) / 2
    //         );

    //         $rate = $midpoint->gte($nightStart) ? 15 : 10;
    //         $price += ($currentTime->diffInMinutes($next) / 60) * $rate;

    //         $currentTime = $next;
    //     }

    //     return round($price, 2);
    // }

    /**
     * MÉTODO QUE DEVUELVE EL PRECIO SEGÚN SI ESTAMOS EN HORARIO NOCTURNO O NO -> SE CONSIDERA HORARIO NOCTURNO A PARTIR DE LAS 20:00 
     *
     * @param  mixed $startTime
     * @return float
     */
    private function calculatePrice(string $startTime, string $date): float
    {
        $start      = Carbon::createFromFormat('H:i', $startTime);
        $nightStart = $this->getNightStartTime($date);

        return $start->gte($nightStart) ? 16.00 : 12.00;
    }

    /**
     * MÉTODO QUE DEVUELVE LA HORA APROXIMADA A LA QUE ATARDECE EN FUNCIÓN DE LA FECHA PASADA POR PARÁMETRO
     *
     * @param  string $date
     * @return Carbon
     */
    private function getNightStartTime(string $date): Carbon
    {
        $month = Carbon::parse($date)->month;

        // HORAS APROXIMADAS EN LAS QUE ATARDECE EN SEVILLA POR MES -> MESES DE ENERO A DICIEMBRE (1-12)
        $sunsetByMonth = [
            1  => '18:00',
            2  => '18:30',
            3  => '19:15',
            4  => '20:30',
            5  => '21:00',
            6  => '21:30',
            7  => '21:30',
            8  => '21:00',
            9  => '20:00',
            10 => '19:00',
            11 => '18:30',
            12 => '18:00',
        ];

        return Carbon::createFromFormat('H:i', $sunsetByMonth[$month]);
    }

    /**
     * CANCELAR RESERVA
     */
    public function destroy(Request $request, Reservation $reservation)
    {
        // UN JUGADOR SOLO PUEDE CANCELAR SUS PROPIAS RESERVAS
        if ($reservation->user_id !== $request->user()->id) {
            abort(403);
        }

        $reservation->update(['status' => 'cancelled']);

        return redirect()->route('player.reservations.index')
            ->with('success', 'Reserva cancelada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }
}
