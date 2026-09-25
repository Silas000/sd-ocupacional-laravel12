<?php

namespace Tests\Feature;

use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserGuardTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrador_nao_exclui_a_propria_conta(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->delete(route('users.destroy', $admin))
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $admin->id, 'deleted_at' => null]);
    }

    public function test_policy_bloqueia_exclusao_do_ultimo_admin(): void
    {
        $policy = new UserPolicy;
        $unicoAdmin = User::factory()->admin()->create();

        // Ninguém pode excluir a própria conta, muito menos a única do sistema.
        $this->assertFalse($policy->delete($unicoAdmin, $unicoAdmin));

        // Havendo outro admin ativo, a exclusão de um deles é liberada.
        $this->assertTrue($policy->delete($unicoAdmin, User::factory()->admin()->create()));
    }

    public function test_administrador_pode_excluir_funcionario(): void
    {
        $admin = User::factory()->admin()->create();
        $funcionario = User::factory()->funcionario()->create();

        $this->actingAs($admin)->delete(route('users.destroy', $funcionario));

        $this->assertSoftDeleted('users', ['id' => $funcionario->id]);
    }

    public function test_administrador_pode_excluir_outro_admin_quando_ha_reforco(): void
    {
        $admin = User::factory()->admin()->create();
        $outroAdmin = User::factory()->admin()->create();

        $this->actingAs($admin)->delete(route('users.destroy', $outroAdmin));

        $this->assertSoftDeleted('users', ['id' => $outroAdmin->id]);
    }

    public function test_administrador_nao_remove_o_proprio_perfil_de_acesso(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->put(route('users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'funcionario',
        ])->assertSessionHasErrors('role');

        $this->assertSame('admin', $admin->fresh()->role);
    }

    public function test_administrador_nao_promove_a_si_mesmo_para_admin_novamente(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->put(route('users.update', $admin), [
            'name' => 'Nome Atualizado',
            'email' => $admin->email,
            'role' => 'admin',
        ])->assertRedirect(route('users.index'));

        $this->assertSame('Nome Atualizado', $admin->fresh()->name);
    }

    public function test_conta_do_ultimo_admin_nao_pode_ser_excluida_pelo_perfil(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->delete(route('profile.destroy'), ['password' => 'password'])
            ->assertSessionHasErrors('password', 'userDeletion');

        $this->assertDatabaseHas('users', ['id' => $admin->id, 'deleted_at' => null]);
    }

    public function test_cpf_invalido_e_rejeitado(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/users', [
            'name' => 'Colaborador',
            'email' => 'colaborador@example.com',
            'role' => 'funcionario',
            'cpf' => '111.111.111-11',
            'password' => 'Senha@123',
            'password_confirmation' => 'Senha@123',
        ])->assertSessionHasErrors('cpf');
    }

    public function test_cpf_duplicado_e_rejeitado(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['cpf' => '529.982.247-25']);

        $this->actingAs($admin)->post('/users', [
            'name' => 'Outro',
            'email' => 'outro@example.com',
            'role' => 'funcionario',
            'cpf' => '52998224725',
            'password' => 'Senha@123',
            'password_confirmation' => 'Senha@123',
        ])->assertSessionHasErrors('cpf');
    }

    public function test_cpf_e_armazenado_cifrado_e_legivel_pelo_modelo(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/users', [
            'name' => 'Colaborador',
            'email' => 'colaborador@example.com',
            'role' => 'funcionario',
            'cpf' => '529.982.247-25',
            'password' => 'Senha@123',
            'password_confirmation' => 'Senha@123',
        ])->assertRedirect(route('users.index'));

        $bruto = DB::table('users')
            ->where('email', 'colaborador@example.com')
            ->value('cpf');

        $this->assertNotSame('52998224725', $bruto, 'o CPF nÃ£o fica em claro no banco');
        $this->assertStringNotContainsString('52998224725', $bruto);

        $this->assertSame('52998224725', User::where('email', 'colaborador@example.com')->first()->cpf);
    }

    public function test_papel_desconhecido_e_rejeitado(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/users', [
            'name' => 'Intruso',
            'email' => 'intruso@example.com',
            'role' => 'superadmin',
            'password' => 'Senha@123',
            'password_confirmation' => 'Senha@123',
        ])->assertSessionHasErrors('role');
    }
}
