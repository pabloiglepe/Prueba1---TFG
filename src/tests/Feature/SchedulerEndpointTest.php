<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SchedulerEndpointTest extends TestCase
{
    use RefreshDatabase;

    // SECRETO DE PRUEBA USADO EN ESTE CONJUNTO DE TESTS
    private const TEST_SECRET = 'test-cron-secret-123';

    protected function setUp(): void
    {
        parent::setUp();

        // INYECTAR EL SECRETO VÍA config() — LA RUTA USA config('padelsync.cron_secret')
        // NUNCA USAR env() DIRECTAMENTE EN RUTAS (NO ES SOBREESCRIBIBLE EN TESTS)
        config(['padelsync.cron_secret' => self::TEST_SECRET]);

        // EVITAR LLAMADAS REALES A APIS EXTERNAS (OPEN-METEO) DURANTE LOS TESTS
        Http::fake();
    }

    // =========================================================
    // PROTECCIÓN DEL ENDPOINT
    // =========================================================

    /**
     * COMPRUEBA QUE SIN EL HEADER SECRETO SE DEVUELVE 403
     */
    public function test_scheduler_endpoint_returns_403_without_secret_header(): void
    {
        $response = $this->get('/run-scheduler');

        $response->assertForbidden();
    }

    /**
     * COMPRUEBA QUE CON UN SECRETO INCORRECTO SE DEVUELVE 403
     */
    public function test_scheduler_endpoint_returns_403_with_wrong_secret(): void
    {
        $response = $this->withHeaders([
            'X-Cron-Secret' => 'secreto-incorrecto',
        ])->get('/run-scheduler');

        $response->assertForbidden();
    }

    /**
     * COMPRUEBA QUE CON EL SECRETO CORRECTO SE DEVUELVE 200 Y EL TEXTO 'OK'
     */
    public function test_scheduler_endpoint_returns_ok_with_correct_secret(): void
    {
        $response = $this->withHeaders([
            'X-Cron-Secret' => self::TEST_SECRET,
        ])->get('/run-scheduler');

        $response->assertOk();
        $response->assertSee('OK');
    }
}