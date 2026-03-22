<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    // PERMITIMOS QUE LARAVEL ESCRIBA EN LA COLUMNA
    protected $fillable = ['name'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
