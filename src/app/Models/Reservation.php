<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'court_id',
        'reservation_date',
        'start_time',
        'end_time',
        'total_price',
        'status',
        'notes',
    ];

    /**
     * UNA RESERVA PERTENECE A UN USUARIO
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * UNA RESERVA PERTENECE A UNA PISTA
     *
     * @return void
     */
    public function court()
    {
        return $this->belongsTo(Court::class);
    }
}
