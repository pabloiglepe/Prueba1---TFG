<?php

namespace App\Http\Controllers;

use App\Models\PadelClass;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function index(Request $request)
    {
        $user = $request->user()->load('role');

        $playerData = null;

        if ($user->role->name === 'player') {
            $now = Carbon::now();

            $upcomingReservations = Reservation::where('user_id', $user->id)
                ->where('status', '!=', 'cancelled')
                ->where(function ($q) use ($now) {
                    $q->where('reservation_date', '>', $now->format('Y-m-d'))
                      ->orWhere(function ($q2) use ($now) {
                          $q2->where('reservation_date', $now->format('Y-m-d'))
                             ->where('end_time', '>', $now->format('H:i:s'));
                      });
                })
                ->with('court')
                ->orderBy('reservation_date')
                ->orderBy('start_time')
                ->limit(5)
                ->get();

            $upcomingClasses = PadelClass::whereHas(
                'registered',
                fn($q) => $q->where('user_id', $user->id)->where('status', 'registered')
            )
                ->where('status', '!=', 'cancelled')
                ->where('date', '>=', $now->format('Y-m-d'))
                ->with(['coach', 'court'])
                ->orderBy('date')
                ->limit(3)
                ->get();

            $monthStart = $now->copy()->startOfMonth()->format('Y-m-d');
            $monthEnd   = $now->copy()->endOfMonth()->format('Y-m-d');

            $monthReservationCount = Reservation::where('user_id', $user->id)
                ->where('status', '!=', 'cancelled')
                ->whereBetween('reservation_date', [$monthStart, $monthEnd])
                ->count();

            $monthSpent = (float) Reservation::where('user_id', $user->id)
                ->where('status', '!=', 'cancelled')
                ->whereBetween('reservation_date', [$monthStart, $monthEnd])
                ->sum('total_price');

            $playerData = compact(
                'upcomingReservations',
                'upcomingClasses',
                'monthReservationCount',
                'monthSpent'
            );
        }

        return view('home', compact('user', 'playerData'));
    }
}
