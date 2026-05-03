<?php

namespace App\Exports;

use App\Models\Reservation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;

class RevenueExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{

    public function __construct(
        private ?string $startDate = null,
        private ?string $endDate = null
    ) {}


    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Reservation::with(['court'])
            ->where('status', '!=', 'cancelled')
            ->orderBy('reservation_date');

        if ($this->startDate) {
            $query->where('reservation_date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->where('reservation_date', '<=', $this->endDate);
        }

        return $query->get()
            ->groupBy(fn($r) => Carbon::parse($r->reservation_date)->format('Y-m'))
            ->map(fn($group, $month) => (object)[
                'month'   => Carbon::parse($month)->translatedFormat('F Y'),
                'count'   => $group->count(),
                'revenue' => $group->sum('total_price'),
            ]);
    }

        
    /**
     * FUNCION QUE DEFINE LAS CABECERAS DEL EXCEL EXPORTADO
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Mes',
            'Nº Reservas',
            'Ingresos (€)',
        ];
    }
    
    
    /**
     * FUNCION QUE DEFINE EL CONTENIDO DE LAS FILAS DEL EXCEL EXPORTADO
     *
     * @param  mixed $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->month,
            $row->count,
            number_format($row->revenue, 2),
        ];
    }

  
    /**
     * FUNCIÓN QUE DEFINE EL NOMBRE QUE VA A TENER EL EXCEL EXPORTADO
     *
     * @return string
     */
    public function title(): string
    {
        return 'Ingresos por mes';
    }
}
