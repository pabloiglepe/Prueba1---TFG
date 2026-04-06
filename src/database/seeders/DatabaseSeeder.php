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
        $adminRole = Role::firstOrCreate([
            'name' => 'admin'
        ]);
        $playerRole = Role::firstOrCreate([
            'name' => 'player'
        ]);
        $coachRole = Role::firstOrCreate([
            'name' => 'coach'
        ]);



        // CREAR USUARIO ADMINISTRADOR
        User::firstOrCreate(
            ['email' => 'admin@padel.com'],
            [
                'role_id' => $adminRole->id,
                'name' => 'admin_padel',
                'password' => Hash::make('Admin_padel123'),
                'phone_number' => '666555444',
                'rgpd_consent' => true
            ]
        );

        // CREAR ENTRENDARO DE PRUEBA
        User::firstOrCreate(
            ['email' => 'coach@padel.com'],
            [
                'role_id'      => $coachRole->id,
                'name'         => 'coach_padel',
                'password'     => Hash::make('Coach_padel123'),
                'phone_number' => '677777777',
                'rgpd_consent' => true,
            ]
        );

        // CREAMOS USUARIO DE PRUEBAS
        User::firstOrCreate(
            ['email' => 'pepe@gmail.com'],
            [
                'role_id' => $playerRole->id,
                'name' => 'pepe_padel',
                'password' => Hash::make('Pepe123'),
                'phone_number' => '611111111',
                'rgpd_consent' => true
            ]
        );

        // CREAR PISTAS INICIALES
        Court::firstOrCreate(
            ['name' => 'Pista Central'],
            [
                'type' => 'cristal',
                'surface' => 'cesped',
                'is_active' => true,
            ]
        );

        Court::firstOrCreate(
            ['name' => 'Pista 2'],
            [
                'type' => 'muro',
                'surface' => 'cesped',
                'is_active' => true,
            ]
        );

        Court::firstOrCreate(
            ['name' => 'Pista 3'],
            [
                'type' => 'cristal',
                'surface' => 'cemento',
                'is_active' => false
            ]
        );
    }
}
