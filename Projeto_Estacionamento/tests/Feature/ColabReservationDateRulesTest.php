<?php

namespace Tests\Feature;

use App\Models\Lugar;
use App\Models\Reserva;
use App\Models\Utilizador;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Tests\TestCase;

class ColabReservationDateRulesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_colab_can_reserve_for_today_until_10am(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-02-24 09:30:00'));

        $user = $this->createColab();
        $lugar = $this->createReservableLugar();

        $response = $this->actingAs($user, 'utilizador')->post('/reservas', [
            'data' => '2026-02-24',
            'lugar_id' => $lugar->id,
        ]);

        $response->assertRedirect('/reservas');
        $this->assertDatabaseHas('reserva', [
            'utilizador_id' => $user->id,
            'lugar_id' => $lugar->id,
            'data' => '2026-02-24',
            'estado' => 'ATIVA',
        ]);
    }

    public function test_colab_cannot_reserve_for_today_after_10am(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-02-24 10:01:00'));

        $user = $this->createColab();
        $lugar = $this->createReservableLugar();

        $response = $this->from('/reservas/criar')
            ->actingAs($user, 'utilizador')
            ->post('/reservas', [
                'data' => '2026-02-24',
                'lugar_id' => $lugar->id,
            ]);

        $response->assertRedirect('/reservas/criar');
        $response->assertSessionHasErrors([
            'data' => 'Para hoje, a reserva só pode ser feita até às 10:00.',
        ]);
        $this->assertDatabaseCount('reserva', 0);
    }

    public function test_colab_can_reserve_until_next_week_friday(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-02-24 09:00:00'));

        $user = $this->createColab();
        $lugar = $this->createReservableLugar();

        $response = $this->actingAs($user, 'utilizador')->post('/reservas', [
            'data' => '2026-03-06',
            'lugar_id' => $lugar->id,
        ]);

        $response->assertRedirect('/reservas');
        $this->assertDatabaseHas('reserva', [
            'utilizador_id' => $user->id,
            'data' => '2026-03-06',
            'estado' => 'ATIVA',
        ]);
    }

    public function test_colab_cannot_reserve_after_next_week_friday(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-02-24 09:00:00'));

        $user = $this->createColab();
        $lugar = $this->createReservableLugar();

        $response = $this->from('/reservas/criar')
            ->actingAs($user, 'utilizador')
            ->post('/reservas', [
                'data' => '2026-03-09',
                'lugar_id' => $lugar->id,
            ]);

        $response->assertRedirect('/reservas/criar');
        $response->assertSessionHasErrors([
            'data' => 'Pode reservar apenas para o resto desta semana e para a semana seguinte.',
        ]);
        $this->assertDatabaseCount('reserva', 0);
    }

    public function test_colab_cannot_reserve_on_weekend(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-02-24 09:00:00'));

        $user = $this->createColab();
        $lugar = $this->createReservableLugar();

        $response = $this->from('/reservas/criar')
            ->actingAs($user, 'utilizador')
            ->post('/reservas', [
                'data' => '2026-02-28',
                'lugar_id' => $lugar->id,
            ]);

        $response->assertRedirect('/reservas/criar');
        $response->assertSessionHasErrors([
            'data' => 'Sábado e domingo estão indisponíveis para reserva.',
        ]);
        $this->assertDatabaseCount('reserva', 0);
    }

    private function createColab(): Utilizador
    {
        return Utilizador::create([
            'nome' => 'Teste Colab',
            'email' => 'colab' . uniqid() . '@example.com',
            'password' => 'password123',
            'role' => 'COLAB',
            'pontos' => 30,
            'obrigar_mudar_password' => false,
        ]);
    }

    private function createReservableLugar(): Lugar
    {
        return Lugar::create([
            'numero' => 3,
            'ativo' => true,
        ]);
    }
}
