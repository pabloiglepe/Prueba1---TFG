<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\WeatherCache;
use Carbon\Carbon;

class FetchWeatherData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather:fetch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Obtiene datos meteorológicos de Open-Meteo para los próximos 14 días y los guarda en weather_cache';


    // COORDENADAS DE SEVILLA
    const LATITUDE  = 37.39;
    const LONGITUDE = -5.99;
    const TIMEZONE  = 'Europe/Madrid';
    const DAYS      = 14;


    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $response = Http::timeout(10)->get('https://api.open-meteo.com/v1/forecast', [
                'latitude'       => self::LATITUDE,
                'longitude'      => self::LONGITUDE,
                'daily'          => 'sunrise,sunset,precipitation_sum',
                'timezone'       => self::TIMEZONE,
                'forecast_days'  => self::DAYS,
            ]);

            if (!$response->successful()) {
                Log::error('weather:fetch —> respuesta no exitosa de Open-Meteo', [
                    'status' => $response->status(),
                ]);
                $this->error('Error al intentar contactar con Open-Meteo. Código: ' . $response->status());
                return self::FAILURE;
            }

            $data  = $response->json();
            $daily = $data['daily'] ?? null;

            if (!$daily || empty($daily['time'])) {
                Log::error('weather:fetch —> respuesta vacía o datos devueltos de manera incorrecta por parte de Open-Meteo');
                $this->error('Respuesta inesperada de Open-Meteo.');
                return self::FAILURE;
            }

            $now   = Carbon::now();
            $count = 0;

            foreach ($daily['time'] as $index => $date) {

                $sunrise       = Carbon::parse($daily['sunrise'][$index])->format('H:i:s');
                $sunset        = Carbon::parse($daily['sunset'][$index])->format('H:i:s');
                $precipitation = $daily['precipitation_sum'][$index] ?? 0.0;

                // INSERTAMOS O ACTUALIZAMOS EL REGISTRO DEL DÍA
                WeatherCache::updateOrCreate(
                    ['date' => $date],
                    [
                        'sunrise'          => $sunrise,
                        'sunset'           => $sunset,
                        'precipitation_mm' => $precipitation,
                        'fetched_at'       => $now,
                    ]
                );

                $count++;
            }

            // LIMPIAMOS REGISTROS CON MÁS DE 2 DÍAS DE ANTIGÜEDAD PARA NO ACUMULAR BASURA
            WeatherCache::where('date', '<', Carbon::today()->subDays(2)->toDateString())->delete();

            $this->info("weather:fetch completado: {$count} días actualizados.");
            Log::info("weather:fetch completado: {$count} días actualizados.");

            return self::SUCCESS;
        } catch (\Throwable $th) {
            Log::error('weather:fetch — excepción inesperada', ['error' => $th->getMessage()]);
            $this->error('Excepción: ' . $th->getMessage());
            return self::FAILURE;
        }
    }
}
