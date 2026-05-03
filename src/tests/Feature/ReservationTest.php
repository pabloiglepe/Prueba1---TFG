<?php

namespace Tests\Feature;

use App\Models\Court;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationTest extends TestCase
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
     * CREA UNA RESERVA PARA MAÑANA A LAS 10:00 PARA EL USUARIO DADO
     */
    private function createReservation(User $user, Court $court): Reservation
    {
        return Reservation::create([
            'user_id'          => $user->id,
            'court_id'         => $court->id,
            'reservation_date' => now()->addDay()->format('Y-m-d'),
            'start_time'       => '10:00:00',
            'end_time'         => '11:30:00',
            'total_price'      => 12.00,
            'status'           => 'pending',
        ]);
    }

    // =========================================================
    // ACCESO A LA VISTA DE RESERVAS
    // =========================================================

    /**
     * COMPRUEBA QUE UN JUGADOR PUEDE VER LA PÁGINA DE RESERVAS
     */
    public function test_player_can_view_reservations_page(): void
    {
        $player = User::factory()->player()->create();

        $response = $this->actingAs($player)->get('/player/reservations');

        $response->assertOk();
    }

    /**
     * COMPRUEBA QUE UN COACH NO PUEDE ACCEDER A LA PÁGINA DE RESERVAS DE JUGADOR
     */
    public function test_coach_cannot_access_player_reservations_page(): void
    {
        $coach = User::factory()->coach()->create();

        $response = $this->actingAs($coach)->get('/player/reservations');

        $response->assertForbidden();
    }

    // =========================================================
    // CANCELACIÓN DE RESERVAS
    // =========================================================

    /**
     * COMPRUEBA QUE UN JUGADOR PUEDE CANCELAR SU PROPIA RESERVA
     */
    public function test_player_can_cancel_own_reservation(): void
    {
        $player = User::factory()->player()->create();
        $court  = $this->createCourt();
        $reservation = $this->createReservation($player, $court);

        $response = $this->actingAs($player)
            ->delete("/player/reservations/{$reservation->id}");

        // SE ESPERA REDIRECCIÓN TRAS CANCELAR CORRECTAMENTE
        $response->assertRedirect();

        $this->assertDatabaseHas('reservations', [
            'id'     => $reservation->id,
            'status' => 'cancelled',
        ]);
    }

    /**
     * COMPRUEBA QUE UN JUGADOR NO PUEDE CANCELAR LA RESERVA DE OTRO JUGADOR
     */
    public function test_player_cannot_cancel_another_players_reservation(): void
    {
        $owner = User::factory()->player()->create();
        $other = User::factory()->player()->create();
        $court = $this->createCourt();
        $reservation = $this->createReservation($owner, $court);

        $response = $this->actingAs($other)
            ->delete("/player/reservations/{$reservation->id}");

        // SE ESPERA 403 O REDIRECCIÓN CON ERROR, NUNCA CANCELACIÓN EXITOSA
        $this->assertDatabaseMissing('reservations', [
            'id'     => $reservation->id,
            'status' => 'cancelled',
        ]);
    }
}
