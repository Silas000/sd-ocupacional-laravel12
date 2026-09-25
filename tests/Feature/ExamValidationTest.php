<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\Risk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamValidationTest extends TestCase
{
    use RefreshDatabase;

    private function medico(): User
    {
        return User::factory()->medico()->create();
    }

    private function colaborador(): User
    {
        return User::factory()->create();
    }

    public function test_status_fora_do_enum_e_rejeitado(): void
    {
        $medico = $this->medico();

        $this->actingAs($medico)->post('/exams', [
            'user_id' => $this->colaborador()->id,
            'tipo' => 'periodico',
            'data_exame' => now()->toDateString(),
            'status' => 'aprovado',
        ])->assertSessionHasErrors('status');
    }

    public function test_tipo_fora_do_enum_e_rejeitado(): void
    {
        $medico = $this->medico();

        $this->actingAs($medico)->post('/exams', [
            'user_id' => $this->colaborador()->id,
            'tipo' => 'semanal',
            'data_exame' => now()->toDateString(),
            'status' => 'pendente',
        ])->assertSessionHasErrors('tipo');
    }

    public function test_severidade_fora_do_enum_e_rejeitada(): void
    {
        $tecnico = User::factory()->tecnico()->create();

        $this->actingAs($tecnico)->post('/incidents', [
            'user_id' => $this->colaborador()->id,
            'data_ocorrencia' => now()->toDateString(),
            'local' => 'GalpÃ£o',
            'descricao' => 'Algo',
            'severidade' => 'critica',
            'tipo' => 'incidente',
        ])->assertSessionHasErrors('severidade');
    }

    public function test_categoria_fora_do_enum_e_rejeitada(): void
    {
        $tecnico = User::factory()->tecnico()->create();

        $this->actingAs($tecnico)->post('/risks', [
            'user_id' => $this->colaborador()->id,
            'nome' => 'Risco',
            'severidade' => 'medio',
            'categoria' => 'radiacao',
        ])->assertSessionHasErrors('categoria');
    }

    public function test_vencimento_anterior_ao_exame_e_rejeitado(): void
    {
        $medico = $this->medico();

        $this->actingAs($medico)->post('/exams', [
            'user_id' => $this->colaborador()->id,
            'tipo' => 'periodico',
            'data_exame' => now()->toDateString(),
            'data_vencimento' => now()->subDay()->toDateString(),
            'status' => 'pendente',
        ])->assertSessionHasErrors('data_vencimento');
    }

    public function test_vencimento_e_calculado_pela_periodicidade_do_exame_periodico(): void
    {
        $medico = $this->medico();

        $this->actingAs($medico)->post('/exams', [
            'user_id' => $this->colaborador()->id,
            'tipo' => 'periodico',
            'data_exame' => now()->toDateString(),
            'status' => 'pendente',
        ])->assertRedirect(route('exams.index'));

        $exam = Exam::latest('id')->first();

        $this->assertSame(
            now()->toDateString(),
            $exam->data_exame->toDateString()
        );
        $this->assertSame(
            now()->addYear()->toDateString(),
            $exam->data_vencimento->toDateString()
        );
    }

    public function test_exame_admissional_nao_recebe_vencimento_automatico(): void
    {
        $medico = $this->medico();

        $this->actingAs($medico)->post('/exams', [
            'user_id' => $this->colaborador()->id,
            'tipo' => 'admissional',
            'data_exame' => now()->toDateString(),
            'status' => 'pendente',
        ]);

        $this->assertNull(Exam::latest('id')->first()->data_vencimento);
    }

    public function test_risco_desmarcado_e_salvo_como_inativo(): void
    {
        $tecnico = User::factory()->tecnico()->create();

        $this->actingAs($tecnico)->post('/risks', [
            'user_id' => $this->colaborador()->id,
            'nome' => 'RuÃ­do',
            'severidade' => 'medio',
            'categoria' => 'fisico',
        ])->assertRedirect(route('risks.index'));

        $this->assertFalse((bool) Risk::latest('id')->first()->ativo);
    }
}
