<?php

namespace App\Providers;

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
    }
}
