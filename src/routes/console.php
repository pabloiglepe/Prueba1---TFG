<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');



// COMANDOS QUE SE EJECUTAN CADA 15 MINUTOS PARA DETERMINAR EL ESTADO DE LAS RESERVAS Y DE LAS CLASES
app(Schedule::class)->command('classes:complete-finished')->everyFifteenMinutes();
app(Schedule::class)->command('reservations:complete-finished')->everyFifteenMinutes();
