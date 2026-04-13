<?php

namespace App\Console\Commands;

use App\Models\PadelClass;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CompleteFinishedClasses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'classes:complete-finished';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marca como completadas las clases cuyo horario ya ha finalizado';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        $updated = PadelClass::where('status', 'registered')
            ->where(function ($q) use ($now) {
                $q->where('date', '<', $now->toDateString())
                    ->orWhere(function ($q2) use ($now) {
                        $q2->where('date', $now->toDateString())
                            ->where('end_time', '<=', $now->format('H:i:s'));
                    });
            })
            ->update(['status' => 'completed']);

        $this->info("Clases completadas: {$updated}");
    }
}
