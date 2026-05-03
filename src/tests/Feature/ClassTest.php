<?php

namespace Tests\Feature;

use App\Models\ClassRegistration;
use App\Models\Court;
use App\Models\PadelClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================
    // HELPERS PRIVADOS
    // =========================================================

    /**
     * CREA UNA PISTA INTERIOR ACTIVA PARA LOS TESTS
     */
    private function createCourt(): Court
    {
        return Court::create([
            'name'       => 'Pista Test',
            'type'       => 'cristal',
            'surface'    => 'cesped',
            'is_active'  => true,
            'is_outdoor' => false,
        ]);
    }

    /**
     * CREA UNA CLASE GRUPAL PÚBLICA PARA MAÑANA A LAS 10:00
     */
    private function createPublicClass(User $coach, Court $court): PadelClass
    {
        return PadelClass::create([
            'coach_id'    => $coach->id,
            'court_id'    => $court->id,
            'title'       => 'Clase de Iniciación',
            'type'        => 'group',
            'level'       => 'initiation',
            'visibility'  => 'public',
            'status'      => 'registered',
            'date'        => now()->addDay()->format('Y-m-d'),
            'start_time'  => '10:00:00',
            'end_time'    => '11:30:00',
            'max_players' => 4,
            'price'       => 20.00,
        ]);
    }

    // =========================================================
    // ACCESO AL MÓDULO DE CLASES (COACH)
    // =========================================================

    /**
     * COMPRUEBA QUE UN COACH PUEDE VER SU LISTADO DE CLASES
     */
    public function test_coach_can_view_their_classes(): void
    {
        $coach = User::factory()->coach()->create();

        $response = $this->actingAs($coach)->get('/coach/classes');

        $response->assertOk();
    }

    /**
     * COMPRUEBA QUE UN PLAYER NO PUEDE ACCEDER A LA CREACIÓN DE CLASES DEL COACH
     */
    public function test_player_cannot_access_coach_class_creation(): void
    {
        $player = User::factory()->player()->create();

        $response = $this->actingAs($player)->get('/coach/classes/create');

        $response->assertForbidden();
    }

    // =========================================================
    // ACCESO AL MÓDULO DE CLASES (PLAYER)
    // =========================================================

    /**
     * COMPRUEBA QUE UN JUGADOR PUEDE VER EL LISTADO DE CLASES DISPONIBLES
     */
    public function test_player_can_view_classes_page(): void
    {
        $player = User::factory()->player()->create();

        $response = $this->actingAs($player)->get('/player/classes');

        $response->assertOk();
    }

    // =========================================================
    // INSCRIPCIÓN EN CLASES
    // =========================================================

    /**
     * COMPRUEBA QUE UN JUGADOR PUEDE INSCRIBIRSE EN UNA CLASE PÚBLICA
     */
    public function test_player_can_register_for_a_public_class(): void
    {
        $coach  = User::factory()->coach()->create();
        $player = User::factory()->player()->create();
        $court  = $this->createCourt();
        $class  = $this->createPublicClass($coach, $court);

        $response = $this->actingAs($player)
            ->post("/player/classes/{$class->id}/register");

        // SE ESPERA REDIRECCIÓN TRAS INSCRIPCIÓN CORRECTA
        $response->assertRedirect();

        // EL VALOR REAL DEL STATUS EN LA BD ES 'registered', NO 'enrolled'
        $this->assertDatabaseHas('classes_reservations', [
            'class_id' => $class->id,
            'user_id'  => $player->id,
            'status'   => 'registered',
        ]);
    }

    /**
     * COMPRUEBA QUE UN JUGADOR NO PUEDE INSCRIBIRSE DOS VECES EN LA MISMA CLASE
     */
    public function test_player_cannot_register_twice_for_same_class(): void
    {
        $coach  = User::factory()->coach()->create();
        $player = User::factory()->player()->create();
        $court  = $this->createCourt();
        $class  = $this->createPublicClass($coach, $court);

        // PRIMERA INSCRIPCIÓN DIRECTAMENTE EN BD — USAR EL VALOR REAL DEL ENUM
        ClassRegistration::create([
            'class_id' => $class->id,
            'user_id'  => $player->id,
            'status'   => 'registered',
        ]);

        // SEGUNDA INSCRIPCIÓN VÍA HTTP — EL CONTROLADOR DEBE RECHAZARLA
        $this->actingAs($player)
            ->post("/player/classes/{$class->id}/register");

        // SOLO DEBE EXISTIR UNA INSCRIPCIÓN EN LA BD
        $this->assertDatabaseCount('classes_reservations', 1);
    }
}