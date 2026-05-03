<?php

namespace Tests\Feature;

use App\Models\Court;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourtTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================
    // HELPERS PRIVADOS
    // =========================================================

    /**
     * CREA UNA PISTA ACTIVA PARA LOS TESTS
     */
    private function createCourt(bool $isOutdoor = false): Court
    {
        return Court::create([
            'name'       => 'Pista Test',
            'type'       => 'cristal',
            'surface'    => 'cesped',
            'is_active'  => true,
            'is_outdoor' => $isOutdoor,
        ]);
    }

    // =========================================================
    // ACCESO AL MÓDULO DE PISTAS
    // =========================================================

    /**
     * COMPRUEBA QUE UN ADMINISTRADOR PUEDE VER EL LISTADO DE PISTAS
     */
    public function test_admin_can_view_courts_list(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin/courts');

        $response->assertOk();
    }

    /**
     * COMPRUEBA QUE UN JUGADOR NO PUEDE ACCEDER A LA GESTIÓN DE PISTAS
     */
    public function test_player_cannot_access_admin_courts(): void
    {
        $player = User::factory()->player()->create();

        $response = $this->actingAs($player)->get('/admin/courts');

        $response->assertForbidden();
    }

    /**
     * COMPRUEBA QUE UN COACH NO PUEDE ACCEDER A LA GESTIÓN DE PISTAS
     */
    public function test_coach_cannot_access_admin_courts(): void
    {
        $coach = User::factory()->coach()->create();

        $response = $this->actingAs($coach)->get('/admin/courts');

        $response->assertForbidden();
    }

    // =========================================================
    // CREACIÓN DE PISTAS
    // =========================================================

    /**
     * COMPRUEBA QUE UN ADMINISTRADOR PUEDE CREAR UNA PISTA
     */
    public function test_admin_can_create_a_court(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/courts', [
            'name'       => 'Pista Nueva',
            'type'       => 'cristal',
            'surface'    => 'cesped',
            'is_active'  => true,
            'is_outdoor' => false,
        ]);

        // SE ESPERA REDIRECCIÓN TRAS CREAR CORRECTAMENTE
        $response->assertRedirect();

        $this->assertDatabaseHas('courts', [
            'name' => 'Pista Nueva',
        ]);
    }

    // =========================================================
    // DESACTIVACIÓN DE PISTAS CON RESERVAS FUTURAS
    // =========================================================

    /**
     * COMPRUEBA QUE NO SE PUEDE DESACTIVAR UNA PISTA CON RESERVAS FUTURAS
     */
    public function test_court_with_future_reservations_cannot_be_deactivated(): void
    {
        $admin  = User::factory()->admin()->create();
        $player = User::factory()->player()->create();
        $court  = $this->createCourt();

        // CREAR UNA RESERVA FUTURA EN LA PISTA
        Reservation::create([
            'user_id'          => $player->id,
            'court_id'         => $court->id,
            'reservation_date' => now()->addDays(3)->format('Y-m-d'),
            'start_time'       => '10:00:00',
            'end_time'         => '11:30:00',
            'total_price'      => 12.00,
            'status'           => 'pending',
        ]);

        $response = $this->actingAs($admin)->patch("/admin/courts/{$court->id}", [
            'name'      => $court->name,
            'is_active' => false,
        ]);

        // LA PISTA DEBE SEGUIR ACTIVA DESPUÉS DEL INTENTO DE DESACTIVACIÓN
        $this->assertDatabaseHas('courts', [
            'id'        => $court->id,
            'is_active' => true,
        ]);
    }
}
