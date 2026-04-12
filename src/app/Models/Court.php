<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection $reservations
 * @method \Illuminate\Database\Eloquent\Relations\HasMany reservations()
 */
class Court extends Model
{
    protected $fillable = [
        'name',
        'type',
        'surface',
        'is_active'
    ];

    /**
     * Una pista puede tener muchas reservas
     *
     * @return void
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
