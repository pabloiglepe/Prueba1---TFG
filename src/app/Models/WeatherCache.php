<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class WeatherCache extends Model
{
    // NOMBRE DE LA TABLA
    protected $table = 'weather_cache';


    // UMBRAL DE LLUVIA A PARTIR DEL CUAL SE CONSIDERA PISTA EXTERIOR NO DISPONIBLE
    const RAIN_MM = 1.0;

    public $timestamps = false;

    // LA CLAVE PRIMARIA ES LA FECHA
    protected $primaryKey = 'date';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'date',
        'sunrise',
        'sunset',
        'precipitation_mm',
        'fetched_at',
    ];

    protected $casts = [
        'date'             => 'date',
        'precipitation_mm' => 'float',
        'fetched_at'       => 'datetime',
    ];

    /**
     * COMPRUEBA SI HAY LLUVIA SUFICIENTE PARA BLOQUEAR PISTAS OUTDOOR
     */
    public function isRainy(): bool
    {
        return $this->precipitation_mm >= self::RAIN_MM;
    }

    /**
     * COMPRUEBA SI UNA HORA DADA ESTÁ EN HORARIO NOCTURNO
     */
    public function isNightTime(string $time): bool
    {
        $check  = Carbon::createFromFormat('H:i', $time);
        $sunset = Carbon::createFromFormat('H:i:s', $this->sunset);

        return $check->greaterThanOrEqualTo($sunset);
    }

    /**
     * OBTIENE EL REGISTRO PARA UNA FECHA CONCRETA O DEVUELVE NULL
     */
    public static function forDate(string $date): ?self
    {
        return self::find($date);
    }
}