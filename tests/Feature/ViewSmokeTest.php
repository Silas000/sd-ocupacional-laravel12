<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\HealthRecord;
use App\Models\Incident;
use App\Models\Risk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Garante que todas as telas renderizam: as views de formulário dependem
 * das listas de opções entregues pelo controller, então um `create` que
 * esqueça de passar uma variável quebraria em produção.
 */
class ViewSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_telas_de_exames_renderizam(): void
    {
        $medico = User::factory()->medico()->create();
        $colaborador = User::factory()->create();

        $exam = Exam::create([
            'user_id' => $colaborador->id,
            'tipo' => 'periodico',
            'data_exame' => now()->toDateString(),
            'status' => 'pendente',
        ]);

        $this->actingAs($medico)->get('/exams')->assertOk()->assertSee('Pendente');
        $this->actingAs($medico)->get('/exams/create')->assertOk()->assertSee('Periódico');
        $this->actingAs($medico)->get(route('exams.show', $exam))->assertOk();
        $this->actingAs($medico)->get(route('exams.edit', $exam))->assertOk()->assertSee('Realizado');
    }

    public function test_telas_de_prontuario_renderizam(): void
    {
        $medico = User::factory()->medico()->create();
        $colaborador = User::factory()->create();

        $registro = HealthRecord::create([
            'user_id' => $colaborador->id,
            'data_registro' => now()->toDateString(),
            'descricao' => 'Consulta de rotina',
            'tipo' => 'consulta',
        ]);

        $this->actingAs($medico)->get('/health')->assertOk();
        $this->actingAs($medico)->get('/health/create')->assertOk();
        $this->actingAs($medico)->get(route('health.show', $registro))->assertOk();
        $this->actingAs($medico)->get(route('health.edit', $registro))->assertOk();
    }

    public function test_telas_de_riscos_renderizam(): void
    {
        $tecnico = User::factory()->tecnico()->create();
        $colaborador = User::factory()->create();

        $risco = Risk::create([
            'user_id' => $colaborador->id,
            'setor' => 'Produção',
            'nome' => 'Ruído acima do limite',
            'severidade' => 'medio',
            'categoria' => 'fisico',
        ]);

        $this->actingAs($tecnico)->get('/risks')->assertOk();
        $this->actingAs($tecnico)->get('/risks/create')->assertOk()->assertSee('Ergonômico');
        $this->actingAs($tecnico)->get(route('risks.show', $risco))->assertOk();
        $this->actingAs($tecnico)->get(route('risks.edit', $risco))->assertOk()->assertSee('Alto');
    }

    public function test_telas_de_ocorrencias_renderizam(): void
    {
        $tecnico = User::factory()->tecnico()->create();
        $colaborador = User::factory()->create();

        $ocorrencia = Incident::create([
            'user_id' => $colaborador->id,
            'data_ocorrencia' => now(),
            'local' => 'Galpão 2',
            'descricao' => 'Queda de empilhadeira',
            'severidade' => 'grave',
            'tipo' => 'acidente',
        ]);

        $this->actingAs($tecnico)->get('/incidents')->assertOk();
        $this->actingAs($tecnico)->get('/incidents/create')->assertOk()->assertSee('Quase acidente');
        $this->actingAs($tecnico)->get(route('incidents.show', $ocorrencia))->assertOk();
        $this->actingAs($tecnico)->get(route('incidents.edit', $ocorrencia))->assertOk()->assertSee('Grave');
    }

    public function test_telas_de_usuarios_e_auditoria_renderizam(): void
    {
        $admin = User::factory()->admin()->create();
        $colaborador = User::factory()->create(['cpf' => '529.982.247-25', 'setor' => 'Produção']);

        $this->actingAs($admin)->get('/users')->assertOk();
        $this->actingAs($admin)->get('/users/create')->assertOk()->assertSee('Técnico de Segurança');
        $this->actingAs($admin)->get(route('users.show', $colaborador))->assertOk()->assertSee('529.982.247-25');
        $this->actingAs($admin)->get(route('users.edit', $colaborador))->assertOk();
        $this->actingAs($admin)->get('/audits')->assertOk();
    }

    public function test_dashboards_por_papel_renderizam(): void
    {
        $colaborador = User::factory()->create(['setor' => 'Produção']);

        $this->actingAs(User::factory()->admin()->create())->get('/dashboard')->assertOk();
        $this->actingAs(User::factory()->medico()->create(['setor' => 'Produção']))->get('/dashboard')->assertOk();
        $this->actingAs(User::factory()->tecnico()->create())->get('/dashboard')->assertOk();
        $this->actingAs($colaborador)->get('/dashboard')->assertOk();
    }

    public function test_telas_de_senha_obrigatoria_e_perfil_renderizam(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin->fresh())->get('/profile')->assertOk();
        $this->actingAs(User::factory()->mustChangePassword()->create())->get('/force-password-change')->assertOk();
    }
}
