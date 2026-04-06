<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'phone_number',
        'rgpd_consent',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    /**
     * UN USUARIO PERTENECE A UN ROL
     *
     * @return void
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }


    /**
     * UN USUARIO PUEDE TENER MUCHAS RESERVAS 
     *
     * @return void
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }


    /**
     * UN USUARIO PUEDE ESTAR INSCRITO EN MUCHAS CLASES
     *
     * @return void
     */
    public function classEnrollments()
    {
        return $this->hasMany(ClassRegistration::class);
    }


    /**
     * CLASES EN LAS QUE ESTÁ INSCRITO EL JUGADOR
     *
     * @return void
     */
    public function classes()
    {
        return $this->belongsToMany(PadelClass::class, 'classes_reservations')
            ->wherePivot('status', 'registered')
            ->withTimestamps();
    }
}
