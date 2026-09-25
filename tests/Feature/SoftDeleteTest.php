<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SoftDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_exame_excluido_permanece_no_banco(): void
    {
        $medico = User::factory()->medico()->create();
        $colaborador = User::factory()->create();

        $exam = Exam::create([
            'user_id' => $colaborador->id,
            'tipo' => 'periodico',
            'data_exame' => now()->toDateString(),
            'status' => 'pendente',
            'resultado' => 'Apto',
        ]);

        $this->actingAs($medico)->delete(route('exams.destroy', $exam));

        $this->assertSoftDeleted('exams', ['id' => $exam->id]);
        $this->assertNull(Exam::find($exam->id));
    }

    public function test_exame_excluido_some_da_listagem(): void
    {
        $medico = User::factory()->medico()->create();
        $colaborador = User::factory()->create();

        $exam = Exam::create([
            'user_id' => $colaborador->id,
            'tipo' => 'periodico',
            'data_exame' => now()->toDateString(),
            'status' => 'pendente',
            'observacoes' => 'Registro visÃ­vel',
        ]);

        $this->actingAs($medico)->delete(route('exams.destroy', $exam));

        $this->actingAs($medico)
            ->get('/exams')
            ->assertOk()
            ->assertDontSee('Registro visÃ­vel');
    }

    public function test_somente_administrador_restaura(): void
    {
        $medico = User::factory()->medico()->create();
        $colaborador = User::factory()->create();

        $exam = Exam::create([
            'user_id' => $colaborador->id,
            'tipo' => 'periodico',
            'data_exame' => now()->toDateString(),
            'status' => 'pendente',
        ]);

        $this->actingAs($medico)->delete(route('exams.destroy', $exam));

        $this->actingAs($medico)
            ->post(route('exams.restore', $exam->id))
            ->assertForbidden();

        $this->actingAs(User::factory()->admin()->create())
            ->post(route('exams.restore', $exam->id))
            ->assertRedirect(route('exams.index'));

        $this->assertNotNull(Exam::find($exam->id));
    }

    public function test_excluir_usuario_preserva_o_historico_de_saude(): void
    {
        $admin = User::factory()->admin()->create(['role' => 'admin']);
        $medico = User::factory()->medico()->create();
        $colaborador = User::factory()->create();

        $exam = Exam::create([
            'user_id' => $colaborador->id,
            'tipo' => 'periodico',
            'data_exame' => now()->toDateString(),
            'status' => 'realizado',
        ]);

        $this->actingAs($admin)->delete(route('users.destroy', $colaborador));

        $this->assertSoftDeleted('users', ['id' => $colaborador->id]);
        $this->assertDatabaseHas('exams', ['id' => $exam->id]);
        $this->assertDatabaseHas('users', ['id' => $colaborador->id]);
    }
}
