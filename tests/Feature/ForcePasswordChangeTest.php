<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForcePasswordChangeTest extends TestCase
{
    use RefreshDatabase;

    public function test_toda_rota_protegida_exige_a_troca_de_senha(): void
    {
        $user = User::factory()->admin()->mustChangePassword()->create();

        $rotas = [
            '/dashboard',
            '/exams',
            '/risks',
            '/incidents',
            '/health',
            '/users',
            '/profile',
        ];

        foreach ($rotas as $rota) {
            $this->actingAs($user)
                ->get($rota)
                ->assertRedirect(route('force-password-change'));
        }
    }

    public function test_a_tela_de_troca_e_acessivel(): void
    {
        $user = User::factory()->mustChangePassword()->create();

        $this->actingAs($user)->get(route('force-password-change'))->assertOk();
    }

    public function test_o_logout_permanece_disponivel_(): void
    {
        $user = User::factory()->mustChangePassword()->create();

        $this->actingAs($user)->post(route('logout'))->assertRedirect('/');
    }

    public function test_apos_trocar_a_senha_o_acesso_e_liberado(): void
    {
        $user = User::factory()->admin()->mustChangePassword()->create();

        $this->actingAs($user)
            ->post(route('force-password-change.update'), [
                'password' => 'Nova@Senha123',
                'password_confirmation' => 'Nova@Senha123',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertFalse($user->fresh()->must_change_password);
    }

    public function test_a_troca_recusa_senha_fraca(): void
    {
        $user = User::factory()->mustChangePassword()->create();

        $this->actingAs($user)
            ->post(route('force-password-change.update'), [
                'password' => 'fraca',
                'password_confirmation' => 'fraca',
            ])
            ->assertSessionHasErrors('password');

        $this->assertTrue($user->fresh()->must_change_password);
    }
}
