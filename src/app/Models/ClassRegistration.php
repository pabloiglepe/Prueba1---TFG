<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassRegistration extends Model
{
    protected $fillable = [
        'class_id',
        'user_id',
        'status',
    ];


    /**
     * UNA INSCRIPCIÓN PERTENECE A UNA CLASE
     *
     * @return void
     */
    public function padelClass()
    {
        return $this->belongsTo(PadelClass::class, 'class_id');
    }


    /**
     * UNA INSCRIPCIÓN PERTENECE A UN USUARIO
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
