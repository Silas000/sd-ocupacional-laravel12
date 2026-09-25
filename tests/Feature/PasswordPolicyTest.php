<?php

namespace Tests\Feature;

use App\Models\PasswordHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_senha_inicial_fica_no_historico(): void
    {
        $user = User::factory()->create(['password' => 'Original@123']);

        $this->assertDatabaseHas('password_history', ['user_id' => $user->id]);
    }

    public function test_a_senha_vigente_nao_pode_ser_reaproveitada(): void
    {
        $user = User::factory()->create(['password' => 'Original@123']);

        $this->actingAs($user)
            ->put(route('password.update'), [
                'current_password' => 'Original@123',
                'password' => 'Original@123',
                'password_confirmation' => 'Original@123',
            ])
            ->assertSessionHasErrorsIn('updatePassword', 'password');
    }

    public function test_a_senha_anterior_nao_pode_ser_reaproveitada(): void
    {
        $user = User::factory()->create(['password' => 'Original@123']);

        $this->actingAs($user)->put(route('password.update'), [
            'current_password' => 'Original@123',
            'password' => 'Trocada@456',
            'password_confirmation' => 'Trocada@456',
        ])->assertRedirect();

        $this->actingAs($user)
            ->put(route('password.update'), [
                'current_password' => 'Trocada@456',
                'password' => 'Original@123',
                'password_confirmation' => 'Original@123',
            ])
            ->assertSessionHasErrorsIn('updatePassword', 'password');
    }

    public function test_troca_bem_sucedida_guarda_a_senha_anterior(): void
    {
        $user = User::factory()->create(['password' => 'Original@123']);

        $this->actingAs($user)->put(route('password.update'), [
            'current_password' => 'Original@123',
            'password' => 'Trocada@456',
            'password_confirmation' => 'Trocada@456',
        ])->assertRedirect();

        $this->assertGreaterThanOrEqual(2, PasswordHistory::where('user_id', $user->id)->count());
    }

    public function test_historico_e_limitado(): void
    {
        $user = User::factory()->create(['password' => 'Original@123']);
        $senhaAtual = 'Original@123';

        for ($i = 1; $i <= 8; $i++) {
            $nova = 'Senha@'.$i.'00x';

            $this->actingAs($user)->put(route('password.update'), [
                'current_password' => $senhaAtual,
                'password' => $nova,
                'password_confirmation' => $nova,
            ])->assertRedirect();

            $senhaAtual = $nova;
        }

        $this->assertLessThanOrEqual(5, PasswordHistory::where('user_id', $user->id)->count());
    }

    public function test_senha_fraca_e_rejeitada(): void
    {
        $user = User::factory()->create(['password' => 'Original@123']);

        $this->actingAs($user)
            ->put(route('password.update'), [
                'current_password' => 'Original@123',
                'password' => 'fraca',
                'password_confirmation' => 'fraca',
            ])
            ->assertSessionHasErrorsIn('updatePassword', 'password');
    }

    public function test_senha_atual_incorreta_e_rejeitada(): void
    {
        $user = User::factory()->create(['password' => 'Original@123']);

        $this->actingAs($user)
            ->put(route('password.update'), [
                'current_password' => 'Errada@123',
                'password' => 'Trocada@456',
                'password_confirmation' => 'Trocada@456',
            ])
            ->assertSessionHasErrorsIn('updatePassword', 'current_password');
    }
}
