<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CompleteFinishedReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:complete-finished';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marca como pagadas las reservas cuyo horario ya ha finalizado';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $now = Carbon::now();

        $updated = Reservation::where('status', 'pending')
            ->where(function ($q) use ($now) {
                $q->where('reservation_date', '<', $now->toDateString())
                    ->orWhere(function ($q2) use ($now) {
                        $q2->where('reservation_date', $now->toDateString())
                            ->where('end_time', '<=', $now->format('H:i:s'));
                    });
            })
            ->update(['status' => 'paid']);

        $this->info("Reservas marcadas como pagadas: {$updated}");
    }
}
