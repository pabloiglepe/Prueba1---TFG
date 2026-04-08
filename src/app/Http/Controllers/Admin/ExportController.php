<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Exports\ReservationsExport;
use App\Exports\RevenueExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    // EXPORTAR RESERVAS    
    /**
     * reservations
     *
     * @param  mixed $request
     * @return void
     */
    public function reservations(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ]);

        $filename = 'reservas-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(
            new ReservationsExport($request->start_date, $request->end_date),
            $filename
        );
    }


    // EXPORTAR INGRESOS    
    /**
     * revenue
     *
     * @param  mixed $request
     * @return void
     */
    public function revenue(Request $request)
    {
        $request->validate([
            'month' => 'nullable|date_format:Y-m',
        ]);

        $startDate = $request->month ? $request->month . '-01' : null;
        $endDate   = $request->month
            ? \Carbon\Carbon::parse($request->month . '-01')->endOfMonth()->format('Y-m-d') : null;

        $filename = 'ingresos-' . ($request->month ?? now()->format('Y-m')) . '.xlsx';

        return Excel::download(
            new RevenueExport($startDate, $endDate),
            $filename
        );
    }
}
