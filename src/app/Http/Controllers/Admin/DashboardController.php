<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        // KPI 1 -> OCUPACIÓN DE PISTAS PARA LAS ÚLTIMAS 8 SEMANAS
        $occupancyData = [];
        $occupancyLabels = [];
        for ($i = 7; $i >= 0; $i--) {
            $week = $now->copy()->subWeeks($i);
            $occupancyLabels[] = 'Sem ' . $week->format('W');
            $occupancyData[] = Reservation::where('status', '!=', 'cancelled')
                ->whereBetween('reservation_date', [
                    $week->copy()->startOfWeek()->format('Y-m-d'),
                    $week->copy()->endOfWeek()->format('Y-m-d'),
                ])
                ->count();
        }


        // KPI 2 -> INGRESOS PARA LOS ÚLTIMOS 6 MESES
        $revenueData   = [];
        $revenueLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $revenueLabels[] = $month->translatedFormat('M Y');
            $revenueData[] = (float) Reservation::where('status', '!=', 'cancelled')
                ->whereYear('reservation_date', $month->year)
                ->whereMonth('reservation_date', $month->month)
                ->sum('total_price');
        }


        // KPI 3 -> ALUMNOS ACTIVOS: JUGADORES CON ALMENOS 1 RESERVA EN LOS ÚLTIMOS 30 DÍAS
        $activePlayersCount = User::whereHas('role', fn($q) => $q->where('name', 'player'))
            ->whereHas(
                'reservations',
                fn($q) => $q
                    ->where('status', '!=', 'cancelled')
                    ->where('reservation_date', '>=', $now->copy()->subDays(30)->format('Y-m-d'))
            )
            ->count();


        // TOTALES PARA LAS TARJETAS RESUMEN
        $totalReservations = Reservation::where('status', '!=', 'cancelled')->count();
        $totalRevenue      = (float) Reservation::where('status', '!=', 'cancelled')->sum('total_price');
        $totalPlayers      = User::whereHas('role', fn($q) => $q->where('name', 'player'))->count();


        // DAROS PARA LOS CLICKS EN LOS GRÁFICOS
        $weekData = [];
        for ($i = 7; $i >= 0; $i--) {
            $week = $now->copy()->subWeeks($i);
            $weekData[] = ['week' => $week->isoWeek(), 'year' => $week->year];
        }

        $monthData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $monthData[] = ['month' => $month->month, 'year' => $month->year];
        }


        // REGISTRO DE ENTRENADORES Y SUS CLASES ACTIVAS
        $coaches = User::whereHas('role', fn($q) => $q->where('name', 'coach'))
            ->with([
                'classesByCoach' => fn($q) => $q
                    ->where('status', 'registered')
                    ->with(['registered'])
                    ->orderBy('date')
            ])
            ->get();


        return view('admin.dashboard', compact(
            'occupancyLabels',
            'occupancyData',
            'revenueLabels',
            'revenueData',
            'activePlayersCount',
            'totalReservations',
            'totalRevenue',
            'totalPlayers',
            'weekData',
            'monthData',
            'coaches'
        ));
    }


    /**
     * FUNCIÓN QUE MUESTRA EL DETALLE DE LAS RESERVAS POR SEMANA
     *
     * @param  Request $request
     * @return json
     */
    public function weekDetail(Request $request)
    {
        $request->validate(['week' => 'required|integer|between:1,53', 'year' => 'required|integer']);

        $date        = Carbon::now()->setISODate($request->year, $request->week);
        $startOfWeek = $date->copy()->startOfWeek()->format('Y-m-d');
        $endOfWeek   = $date->copy()->endOfWeek()->format('Y-m-d');

        // RESERVAS DE LA SEMANA
        $reservations = Reservation::with(['user', 'court'])
            ->where('status', '!=', 'cancelled')
            ->whereBetween('reservation_date', [$startOfWeek, $endOfWeek])
            ->orderBy('reservation_date')
            ->orderBy('start_time')
            ->get()
            ->map(fn($r) => [
                'date'    => Carbon::parse($r->reservation_date)->format('d/m/Y'),
                'player'  => $r->user->name,
                'court'   => $r->court->name,
                'time'    => Carbon::parse($r->start_time)->format('H:i') . ' - ' . Carbon::parse($r->end_time)->format('H:i'),
                'price'   => number_format($r->total_price, 2) . '€',
            ]);

        return response()->json([
            'label'        => 'Semana ' . $request->week . ' de ' . $request->year,
            'reservations' => $reservations,
        ]);
    }



    /**
     * FUNCIÓN QUE MUESTRA EL DETALLE DE INGRESOS POR MES
     *
     * @param  Request $request
     * @return json
     */
    public function monthDetail(Request $request)
    {
        $request->validate(['month' => 'required|integer|between:1,12', 'year' => 'required|integer']);

        // RESERVAS DEL MES
        $reservations = Reservation::with(['user', 'court'])
            ->where('status', '!=', 'cancelled')
            ->whereYear('reservation_date', $request->year)
            ->whereMonth('reservation_date', $request->month)
            ->orderBy('reservation_date')
            ->get()
            ->map(fn($r) => [
                'date'   => Carbon::parse($r->reservation_date)->format('d/m/Y'),
                'player' => $r->user->name,
                'court'  => $r->court->name,
                'time'   => Carbon::parse($r->start_time)->format('H:i') . ' - ' . Carbon::parse($r->end_time)->format('H:i'),
                'price'  => number_format($r->total_price, 2) . '€',
            ]);


        // DESGLOSE POR PISTA
        $byCourtRaw = Reservation::with('court')
            ->where('status', '!=', 'cancelled')
            ->whereYear('reservation_date', $request->year)
            ->whereMonth('reservation_date', $request->month)
            ->get()
            ->groupBy('court_id')
            ->map(fn($group) => [
                'court'    => $group->first()->court->name,
                'total'    => number_format($group->sum('total_price'), 2) . '€',
                'count'    => $group->count(),
            ])
            ->values();

        $label = Carbon::createFromDate($request->year, $request->month, 1)->translatedFormat('F Y');

        return response()->json([
            'label'        => $label,
            'reservations' => $reservations,
            'by_court'     => $byCourtRaw,
        ]);
    }
}
