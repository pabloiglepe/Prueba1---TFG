<?php

namespace App\Exports;

use App\Models\Reservation;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;

class ReservationsExport implements FromCollection, WithHeadings, WithMapping, WithTitle
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
        $query = Reservation::with(['user', 'court'])
            ->where('status', '!=', 'cancelled')
            ->orderBy('reservation_date')
            ->orderBy('start_time');

        if ($this->startDate) {
            $query->where('reservation_date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->where('reservation_date', '<=', $this->endDate);
        }

        return $query->get();
    }


    /**
     * FUNCION QUE DEFINE LAS CABECERAS DEL EXCEL EXPORTADO
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Fecha',
            'Jugador',
            'Email',
            'Pista',
            'Hora_inicio',
            'Hora_fin',
            'Precio',
            'Estado',
        ];
    }

    
    /**
     * FUNCION QUE DEFINE EL CONTENIDO DE LAS FILAS DEL EXCEL EXPORTADO
     *
     * @param  mixed $reservation
     * @return array
     */
    public function map($reservation): array
    {
        return [
            Carbon::parse($reservation->reservation_date)->format('d/m/Y'),
            $reservation->user->name,
            $reservation->user->email,
            $reservation->court->name,
            Carbon::parse($reservation->start_time)->format('H:i'),
            Carbon::parse($reservation->end_time)->format('H:i'),
            number_format($reservation->total_price, 2),
            $reservation->status,
        ];
    }


    /**
     * FUNCIÓN QUE DEFINE EL NOMBRE QUE VA A TENER EL EXCEL EXPORTADO
     *
     * @return string
     */
    public function title(): string
    {
        return 'Reservas';
    }
}
