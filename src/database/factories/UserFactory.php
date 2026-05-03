<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * LA CONTRASEÑA ACTUAL USADA POR LA FACTORY (SE CACHEA ENTRE INSTANCIAS)
     */
    protected static ?string $password;

    /**
     * DEFINE EL ESTADO BASE DEL MODELO
     * USA ROL PLAYER POR DEFECTO PARA QUE LOS TESTS DE BREEZE FUNCIONEN SIN ESTADO EXPLÍCITO
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'              => fake()->name(),
            'email'             => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => static::$password ??= Hash::make('password'),
            'remember_token'    => Str::random(10),
            'phone_number'      => fake()->numerify('6########'),
            'rgpd_consent'      => true,
            // ROL PLAYER POR DEFECTO: EVITA EL NOT NULL CONSTRAINT EN LOS TESTS DE BREEZE
            'role_id'           => Role::firstOrCreate(['name' => 'player'])->id,
        ];
    }

    /**
     * INDICA QUE EL EMAIL DEL USUARIO NO ESTÁ VERIFICADO
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * ESTADO: USUARIO CON ROL ADMINISTRADOR
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Role::firstOrCreate(['name' => 'admin'])->id,
        ]);
    }

    /**
     * ESTADO: USUARIO CON ROL ENTRENADOR
     */
    public function coach(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Role::firstOrCreate(['name' => 'coach'])->id,
        ]);
    }

    /**
     * ESTADO: USUARIO CON ROL JUGADOR (EQUIVALENTE AL ESTADO BASE)
     */
    public function player(): static
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => Role::firstOrCreate(['name' => 'player'])->id,
        ]);
    }
}