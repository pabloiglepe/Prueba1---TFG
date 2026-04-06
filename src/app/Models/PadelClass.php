<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PadelClass extends Model
{
    // TABLA DE BBDD ASOCIADAS AL MODELO
    protected $table = 'classes';

    // 
    protected $fillable = [
        'coach_id',
        'court_id',
        'title',
        'type',
        'level',
        'visibility',
        'status',
        'date',
        'start_time',
        'end_time',
        'max_players',
        'price',
    ];


    /**
     * UNA CLASE PERTENECE A UN ENTRENADOR
     *
     * @return void
     */
    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id');
    }


    /**
     * UNA CLASE SE DA EN UNA PISTA
     *
     * @return void
     */
    public function court()
    {
        return $this->belongsTo(Court::class);
    }


    /**
     * UNA CLASE TIENE MUCHAS INSCRIPCIONES
     *
     * @return void
     */
    public function registered()
    {
        return $this->hasMany(ClassRegistration::class, 'class_id');
    }


    /**
     * ALUMNOS INSCRITOS ACTIVOS
     *
     * @return void
     */
    public function players()
    {
        return $this->belongsToMany(User::class, 'classes_reservations', 'class_id', 'user_id')
            ->wherePivot('status', 'registered')
            ->withTimestamps();
    }


    /**
     * PLAZAS DISPONIBLES
     *
     * @return void
     */
    public function availableSpots(): int
    {
        return $this->max_players - $this->registered()
            ->where('status', 'registered')
            ->count();
    }


    /**
     * VER SI LA CLASE ESTÁ LLENA
     *
     * @return void
     */
    public function isFull(): bool
    {
        return $this->availableSpots() <= 0;
    }
}
