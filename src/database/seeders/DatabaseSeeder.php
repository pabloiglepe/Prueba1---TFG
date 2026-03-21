<?php

namespace Database\Seeders;

use App\Models\Court;
use App\Models\Role;
use App\Models\User;



use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // CREAMOS ROLES
        $adminRole = Role::create([
            'name' => 'admin'
        ]);
        $playerRole = Role::create([
            'name' => 'player'
        ]);



        // CREAR USUARIO ADMINISTRADOR
        User::factory()->create([
            'role_id' => $adminRole->id,
            'name' => 'admin_padel',
            'email' => 'admin@padel.com',
            'password' => Hash::make('Admin_padel123'),
            'phone_number' => '666555444',
            'email' => 'admin@padel.com',
            'rgpd_consent' => true
        ]);

        // CREAMOS USUARIO DE PRUEBAS
        User::create([
            'role_id' => $playerRole->id,
            'name' => 'pepe_padel',
            'email' => 'pepe@gmail.com',
            'password' => Hash::make('Pepe123'),
            'phone_number' => '611111111',
            'rgpd_consent' => true
        ]);

        // CREAR PISTAS INICIALES
        Court::create([
            'name' => 'Pista Central',
            'type' => 'cristal',
            'surface' => 'cesped',
            'is_active' => true,
        ]);

        Court::create([
            'name' => 'Pista 2',
            'type' => 'muro',
            'surface' => 'cesped',
            'is_active' => true,
        ]);

        Court::create([
            'name' => 'Pista 3',
            'type' => 'cristal',
            'surface' => 'cemento',
            // PISTA EN MANTENIMIENTO 
            'is_active' => false
        ]);
    }
}
