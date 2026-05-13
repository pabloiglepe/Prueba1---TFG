<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Models\ClubSetting;
use App\Models\PadelClass;
use App\Models\Reservation;
use App\Models\Court;
use App\Models\WeatherCache;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservationController extends Controller
{

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

        // DATOS METEOROLÓGICOS Y LLUVIA PARA LA FECHA SELECCIONADA
        $weather = null;
        $isRainy = false;

        // CONFIGURACIÓN DEL CLUB (DINÁMICA) -> SIEMPRE DISPONIBLE PARA LA VISTA
        $duration = (int) ClubSetting::get('reservation_duration', 90);
        $slotStep = (int) ClubSetting::get('slot_interval', 30);

        if ($request->filled('date')) {
            $request->validate([
                'date' => 'required|date|after_or_equal:today',
            ]);

            // OBTENEMOS DATOS DEL CLIMA DESDE LA CACHÉ LOCAL (SACADA DE PETICIÓN A OPEN-METEO)
            $weather = WeatherCache::forDate($request->date);
            $isRainy = $weather ? $weather->isRainy() : false;

            // DEFINICIÓN Y GENERACIÓN DE LAS FRANJAS HORARIAS DISPONIBLES
            $openingTime = Carbon::createFromFormat('H:i', ClubSetting::get('opening_time', '09:00'));
            $closingTime = Carbon::createFromFormat('H:i', ClubSetting::get('closing_time', '22:00'));
            $current     = $openingTime->copy();

            while ($current->copy()->addMinutes($duration)->lte($closingTime)) {
                $slots->push($current->format('H:i'));
                $current->addMinutes($slotStep);
            }

            // SI LA FECHA ES HOY, FILTRAMOS LAS FRANJAS QUE YA HAN PASADO
            if (Carbon::parse($request->date)->isToday()) {
                $now = Carbon::now();
                $slots = $slots->filter(function ($slot) use ($now) {
                    return Carbon::createFromFormat('H:i', $slot)->gt($now);
                });
            }

            // PRE-FILTRAMOS LAS FRANJAS: SOLO MANTENEMOS LAS QUE TIENEN ≥1 PISTA LIBRE
            // (CONSIDERANDO RESERVAS + CLASES Y RESPETANDO LA REGLA DE LLUVIA)
            $activeCourtIds = Court::where('is_active', true)
                ->when($isRainy, fn($q) => $q->where('is_outdoor', false))
                ->pluck('id');

            $dayReservations = Reservation::where('reservation_date', $request->date)
                ->where('status', '!=', 'cancelled')
                ->get(['court_id', 'start_time', 'end_time']);

            $dayClasses = PadelClass::where('date', $request->date)
                ->where('status', '!=', 'cancelled')
                ->get(['court_id', 'start_time', 'end_time']);

            $slots = $slots->filter(function ($slot) use ($activeCourtIds, $dayReservations, $dayClasses, $duration) {
                $slotStart = Carbon::createFromFormat('H:i', $slot)->format('H:i:s');
                $slotEnd   = Carbon::createFromFormat('H:i', $slot)->addMinutes($duration)->format('H:i:s');

                $overlapsSlot = fn($busy) => $busy->start_time < $slotEnd && $busy->end_time > $slotStart;

                foreach ($activeCourtIds as $courtId) {
                    $hasReservation = $dayReservations->where('court_id', $courtId)->contains($overlapsSlot);
                    $hasClass       = $dayClasses->where('court_id', $courtId)->contains($overlapsSlot);
                    if (!$hasReservation && !$hasClass) {
                        return true; // hay al menos una pista libre en esta franja
                    }
                }
                return false;
            })->values();

            // SI TAMBIÉN HAY FRANJA SELECCIONADA, BUSCAMOS PISTAS LIBRES
            if ($request->filled('start_time')) {
                $request->validate([
                    'start_time' => 'required|date_format:H:i',
                ]);

                $startTime = $request->start_time;
                $endTime   = Carbon::createFromFormat('H:i', $startTime)
                    ->addMinutes($duration)
                    ->format('H:i');


                // PISTAS OCUPADAS POR CLASES EN ESA FRANJA -> SE EXCLUYEN
                $busyCourtIdsByClass = PadelClass::where('date', $request->date)
                    ->where('status', '!=', 'cancelled')
                    ->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime)
                    ->pluck('court_id');

                $courts = Court::where('is_active', true)
                    ->when($isRainy, fn($q) => $q->where('is_outdoor', false)) // SI HAY LLUVIA, EXCLUIMOS PISTAS EXTERIORES
                    ->whereNotIn('id', $busyCourtIdsByClass)
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

        // HORA DE INICIO DE TARIFA NOCTURNA (REAL DESDE CACHÉ O ESTÁTICO)
        $nightStartTime = $request->filled('date')
            ? $this->getNightStartTime($request->date)->format('H:i')
            : '20:00';

        $priceDay   = (float) ClubSetting::get('price_day', 12.00);
        $priceNight = (float) ClubSetting::get('price_night', 16.00);

        return view('player.reservations.create', compact('slots', 'courts', 'nightStartTime', 'isRainy', 'priceDay', 'priceNight', 'duration'));
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
            ->addMinutes((int) ClubSetting::get('reservation_duration', 90))
            ->format('H:i');

        // DOBLE COMPROBACIÓN DE SOLAPAMIENTO EN EL SERVIDOR -> RESERVAS
        $reservationOverlap = Reservation::where('court_id', $validated['court_id'])
            ->where('reservation_date', $validated['date'])
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            })
            ->exists();

        if ($reservationOverlap) {
            return back()->withErrors([
                'court_id' => 'Esta pista ya está reservada en ese horario.'
            ])->withInput();
        }

        // DOBLE COMPROBACIÓN DE SOLAPAMIENTO EN EL SERVIDOR -> CLASES
        $classOverlap = PadelClass::where('court_id', $validated['court_id'])
            ->where('date', $validated['date'])
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where('start_time', '<', $endTime)
                    ->where('end_time', '>', $startTime);
            })
            ->exists();

        if ($classOverlap) {
            return back()->withErrors([
                'court_id' => 'Esta pista tiene una clase programada en ese horario.'
            ])->withInput();
        }

        // NO PERMITIR RESERVAR PISTA EXTERIOR SI HAY LLUVIA
        $court   = Court::findOrFail($validated['court_id']);
        $weather = WeatherCache::forDate($validated['date']);

        if ($court->is_outdoor && $weather && $weather->isRainy()) {
            return back()->withErrors([
                'court_id' => 'No se puede reservar una pista exterior cuando hay lluvia prevista.'
            ])->withInput();
        }

        Reservation::create([
            'user_id'          => $request->user()->id,
            'court_id'         => $validated['court_id'],
            'reservation_date' => $validated['date'],
            'start_time'       => $startTime,
            'end_time'         => $endTime,
            'total_price'      => $this->calculatePrice($startTime, $validated['date']),
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
     * MÉTODO QUE DEVUELVE EL PRECIO SEGÚN SI ESTAMOS EN HORARIO NOCTURNO O NO
     * UTILIZA EL OCASO REAL DE OPEN-METEO O EL FALLBACK ESTÁTICO
     *
     * @param  string $startTime
     * @param  string $date
     * @return float
     */
    private function calculatePrice(string $startTime, string $date): float
    {
        $start      = Carbon::createFromFormat('H:i', $startTime);
        $nightStart = $this->getNightStartTime($date);

        return $start->gte($nightStart)
            ? (float) ClubSetting::get('price_night', 16.00)
            : (float) ClubSetting::get('price_day', 12.00);
    }

    /**
     * MÉTODO QUE DEVUELVE LA HORA DE OCASO PARA UNA FECHA DADA
     * PRIORIZA EL DATO REAL DE OPEN-METEO (weather_cache) Y CAE AL FALLBACK ESTÁTICO SI NO HAY DATO
     *
     * @param  string $date
     * @return Carbon
     */
    private function getNightStartTime(string $date): Carbon
    {
        // INTENTAMOS OBTENER EL DATO REAL DE OPEN-METEO
        $weather = WeatherCache::forDate($date);

        if ($weather) {
            return Carbon::createFromFormat('H:i:s', $weather->sunset);
        }

        // FALLBACK -> TABLA ESTÁTICA APROXIMADA POR MES EN CASO DE QUE NO HAYA DATOS EN CACHÉ (MÉTODO ANTIGUO)
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