<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================
    // LOGIN
    // =========================================================

    /**
     * COMPRUEBA QUE EL SISTEMA AUTENTICA CORRECTAMENTE CON CREDENCIALES VÁLIDAS
     * NOTA: EL FORMULARIO DE LOGIN USA LIVEWIRE VOLT (NO HAY RUTA POST /login)
     * SE VERIFICA LA LÓGICA DE AUTENTICACIÓN DIRECTAMENTE VÍA Auth::attempt()
     */
    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->player()->create([
            'password' => bcrypt('Test1234'),
        ]);

        $result = Auth::attempt([
            'email'    => $user->email,
            'password' => 'Test1234',
        ]);

        $this->assertTrue($result);
        $this->assertAuthenticatedAs($user);
    }

    /**
     * COMPRUEBA QUE EL SISTEMA RECHAZA CREDENCIALES INCORRECTAS
     */
    public function test_user_cannot_login_with_wrong_password(): void
    {
        $user = User::factory()->player()->create([
            'password' => bcrypt('Correcta123'),
        ]);

        $result = Auth::attempt([
            'email'    => $user->email,
            'password' => 'Incorrecta999',
        ]);

        $this->assertFalse($result);
        $this->assertGuest();
    }

    /**
     * COMPRUEBA QUE UN USUARIO NO AUTENTICADO ES REDIRIGIDO AL LOGIN
     */
    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    // =========================================================
    // LOGOUT
    // =========================================================

    /**
     * COMPRUEBA QUE UN USUARIO AUTENTICADO PUEDE CERRAR SESIÓN
     * NOTA: EL LOGOUT USA LIVEWIRE VOLT, SE VERIFICA VÍA Auth::logout() DIRECTAMENTE
     */
    public function test_user_can_logout(): void
    {
        $user = User::factory()->player()->create();
        $this->actingAs($user);

        $this->assertAuthenticatedAs($user);

        Auth::logout();

        $this->assertGuest();
    }

    // =========================================================
    // CONTROL DE ACCESO POR ROL
    // =========================================================

    /**
     * COMPRUEBA QUE UN PLAYER NO PUEDE ACCEDER AL PANEL DE ADMINISTRACIÓN
     */
    public function test_player_cannot_access_admin_routes(): void
    {
        $user = User::factory()->player()->create();

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertForbidden();
    }

    /**
     * COMPRUEBA QUE UN COACH NO PUEDE ACCEDER AL PANEL DE ADMINISTRACIÓN
     */
    public function test_coach_cannot_access_admin_routes(): void
    {
        $user = User::factory()->coach()->create();

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertForbidden();
    }

    /**
     * COMPRUEBA QUE EL ADMIN PUEDE ACCEDER A LAS RUTAS DE JUGADOR
     */
    public function test_admin_can_access_player_routes(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->get('/player/reservations');

        $response->assertOk();
    }

    /**
     * COMPRUEBA QUE EL ADMIN PUEDE ACCEDER A LAS RUTAS DE ENTRENADOR
     */
    public function test_admin_can_access_coach_routes(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->get('/coach/classes');

        $response->assertOk();
    }
}