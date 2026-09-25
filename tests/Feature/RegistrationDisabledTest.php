<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationDisabledTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_tela_de_cadastro_nao_existe(): void
    {
        $this->get('/register')->assertNotFound();
    }

    public function test_o_cadastro_publico_e_bloqueado(): void
    {
        $this->post('/register', [
            'name' => 'Intruso',
            'email' => 'intruso@example.com',
            'password' => 'Senha@123',
            'password_confirmation' => 'Senha@123',
        ])->assertNotFound();

        $this->assertDatabaseMissing('users', ['email' => 'intruso@example.com']);
    }

    public function test_raiz_redireciona_para_o_login_quando_deslogado(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_raiz_redireciona_para_o_dashboard_quando_autenticado(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/')
            ->assertRedirect(route('dashboard'));
    }
}
