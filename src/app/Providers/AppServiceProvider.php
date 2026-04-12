<?php

namespace App\Providers;

use App\Mail\BrevoTransport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // FUERZA A LARAVEL A USAR HTTPS EN PRODUCCIÓN
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // CONFIGURO EL 'LOCALE' DE CARBON PARA QUE ESTÉ EN ESPAÑOL (MESE, DÍAS, ETC)
        Carbon::setLocale('es');

        Mail::extend('brevo', function () {
            return new BrevoTransport(config('services.brevo.key'));
        });
    }
}
