<?php

namespace Tests\Feature;

use App\Models\Audit;
use App\Models\Exam;
use App\Models\HealthRecord;
use App\Models\Incident;
use App\Models\Risk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenSpout\Reader\XLSX\Reader;
use Tests\TestCase;

/**
 * Os relatórios reaproveitam os filtros das listagens e seguem a mesma
 * divisão de papéis: quem não abre uma área também não a exporta, e toda
 * saída de dados fica registrada.
 */
class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_tela_de_relatorios_lista_somente_o_que_o_papel_permite(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/relatorios')
            ->assertOk()
            ->assertSee('Exames')
            ->assertSee('Colaboradores');

        $this->actingAs(User::factory()->medico()->create())
            ->get('/relatorios')
            ->assertOk()
            ->assertSee('Exames')
            ->assertDontSee('Colaboradores');

        $this->actingAs(User::factory()->tecnico()->create())
            ->get('/relatorios')
            ->assertOk()
            ->assertSee('Riscos')
            ->assertDontSee('Histórico de saúde');
    }

    public function test_funcionario_nao_acessa_a_relatorios(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/relatorios')
            ->assertForbidden();
    }

    public function test_relatorio_de_exames_aplica_o_filtro_de_periodo(): void
    {
        $medico = User::factory()->medico()->create();
        $colaborador = User::factory()->create(['setor' => 'Produção', 'name' => 'Maria Souza']);

        /*
         * Os médicos responsáveis só aparecem nas linhas do relatório,
         * nunca como opção de filtro, então servem para provar quais
         * registros entraram no recorte.
         */
        Exam::create([
            'user_id' => $colaborador->id,
            'tipo' => 'periodico',
            'data_exame' => now()->subMonths(2)->toDateString(),
            'status' => 'realizado',
            'medico_responsavel' => 'Dra. Helena',
        ]);

        Exam::create([
            'user_id' => $colaborador->id,
            'tipo' => 'admissional',
            'data_exame' => now()->subDays(3)->toDateString(),
            'status' => 'pendente',
            'medico_responsavel' => 'Dr. Bruno',
        ]);

        $this->actingAs($medico)
            ->get('/relatorios/exams')
            ->assertOk()
            ->assertSee('Maria Souza')
            ->assertSee('Dra. Helena')
            ->assertSee('Dr. Bruno');

        $this->actingAs($medico)
            ->get('/relatorios/exams?status=pendente')
            ->assertOk()
            ->assertSee('Dr. Bruno')
            ->assertDontSee('Dra. Helena');

        $this->actingAs($medico)
            ->get('/relatorios/exams?de='.now()->subDays(10)->toDateString())
            ->assertOk()
            ->assertSee('Dr. Bruno')
            ->assertDontSee('Dra. Helena');

        $this->actingAs($medico)
            ->get('/relatorios/exams?tipo=admissional')
            ->assertOk()
            ->assertSee('Dr. Bruno')
            ->assertDontSee('Dra. Helena');
    }

    public function test_filtro_por_setor_e_texto_aplicados(): void
    {
        $tecnico = User::factory()->tecnico()->create();

        $producao = User::factory()->create(['setor' => 'Produção', 'name' => 'Joao Producao']);
        $administrativo = User::factory()->create(['setor' => 'Administrativo', 'name' => 'Ana Administrativo']);

        Risk::create([
            'user_id' => $producao->id,
            'setor' => 'Produção',
            'nome' => 'Ruído acima do limite',
            'severidade' => 'alto',
            'categoria' => 'fisico',
        ]);

        Risk::create([
            'user_id' => $administrativo->id,
            'setor' => 'Administrativo',
            'nome' => 'Ergonomia de mesa',
            'severidade' => 'baixo',
            'categoria' => 'ergonomico',
        ]);

        $this->actingAs($tecnico)
            ->get('/relatorios/risks?setor=Administrativo')
            ->assertOk()
            ->assertSee('Ergonomia de mesa')
            ->assertDontSee('Ruído acima do limite');

        $this->actingAs($tecnico)
            ->get('/relatorios/risks?q='.urlencode('Ruído'))
            ->assertOk()
            ->assertSee('Ruído acima do limite')
            ->assertDontSee('Ergonomia de mesa');

        $this->actingAs($tecnico)
            ->get('/relatorios/risks?severidade=alto')
            ->assertOk()
            ->assertSee('Ruído acima do limite');
    }

    public function test_medico_nao_relatorio_de_riscos_e_tecnico_nao_de_saude(): void
    {
        $this->actingAs(User::factory()->medico()->create())
            ->get('/relatorios/risks')
            ->assertNotFound();

        $this->actingAs(User::factory()->tecnico()->create())
            ->get('/relatorios/health')
            ->assertNotFound();
    }

    public function test_relatorio_inexistente_devolve_404(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/relatorios/inexistente')
            ->assertNotFound();
    }

    public function test_exportacao_pdf_e_gerada(): void
    {
        $medico = User::factory()->medico()->create();
        $colaborador = User::factory()->create(['name' => 'Carlos Exportacao']);

        Exam::create([
            'user_id' => $colaborador->id,
            'tipo' => 'periodico',
            'data_exame' => now()->toDateString(),
            'status' => 'realizado',
        ]);

        $resposta = $this->actingAs($medico)->get('/relatorios/exams/pdf');

        $resposta->assertOk();
        $resposta->assertHeader('content-type', 'application/pdf');
        $resposta->assertDownload('relatorio-exams-'.now()->format('Y-m-d').'.pdf');
    }

    public function test_exportacao_excel_e_gerada(): void
    {
        $admin = User::factory()->admin()->create();

        $resposta = $this->actingAs($admin)->get('/relatorios/users/excel');

        $resposta->assertOk();
        $resposta->assertDownload('relatorio-users-'.now()->format('Y-m-d').'.xlsx');
    }

    public function test_arquivo_excel_exportado_e_legivel(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['name' => 'Joao Planilha', 'setor' => 'Produção']);

        $resposta = $this->actingAs($admin)->get('/relatorios/users/excel');
        $resposta->assertOk();

        $caminho = $resposta->baseResponse->getFile()->getPathname();
        $this->assertFileExists($caminho);

        $leitor = new Reader;
        $leitor->open($caminho);

        $cabecalho = null;
        $total = 0;

        foreach ($leitor->getSheetIterator() as $folha) {
            foreach ($folha->getRowIterator() as $linha) {
                $valores = $linha->toArray();

                if (in_array('Nome', $valores, true)) {
                    $cabecalho = $valores;

                    continue;
                }

                // Só conta depois do cabeçalho: as linhas de
                // identificação do relatório também têm texto.
                if ($cabecalho !== null) {
                    $total++;
                }
            }
        }

        $leitor->close();

        $this->assertNotNull($cabecalho, 'a planilha deve ter a linha de cabeçalho');
        $this->assertContains('E-mail', $cabecalho);
        $this->assertSame(2, $total, 'uma linha por usuário exportado');

        unlink($caminho);
    }

    public function test_exportacao_ignora_registros_excluidos_por_padrao(): void
    {
        $admin = User::factory()->admin()->create();
        $colaborador = User::factory()->create();

        $risco = Risk::create([
            'user_id' => $colaborador->id,
            'setor' => 'Produção',
            'nome' => 'Risco Temporario',
            'severidade' => 'medio',
            'categoria' => 'quimico',
        ]);

        $risco->delete();

        $this->actingAs($admin)
            ->get('/relatorios/risks')
            ->assertOk()
            ->assertDontSee('Risco Temporario');

        $this->actingAs($admin)
            ->get('/relatorios/risks?incluir_excluidos=1')
            ->assertOk()
            ->assertSee('Risco Temporario');
    }

    public function test_somente_administrador_filtra_registros_excluidos(): void
    {
        $tecnico = User::factory()->tecnico()->create();
        $colaborador = User::factory()->create();

        $risco = Risk::create([
            'user_id' => $colaborador->id,
            'setor' => 'Produção',
            'nome' => 'Risco Oculto',
            'severidade' => 'medio',
            'categoria' => 'quimico',
        ]);

        $risco->delete();

        $this->actingAs($tecnico)
            ->get('/relatorios/risks?incluir_excluidos=1')
            ->assertOk()
            ->assertDontSee('Risco Oculto');
    }

    public function test_exportacao_registra_auditoria_com_o_recorte(): void
    {
        $admin = User::factory()->admin()->create();
        $colaborador = User::factory()->create(['setor' => 'Produção']);

        HealthRecord::create([
            'user_id' => $colaborador->id,
            'data_registro' => now()->toDateString(),
            'descricao' => 'Retorno ao trabalho',
            'tipo' => 'atestado',
        ]);

        $this->actingAs($admin)->get('/relatorios/health/pdf')->assertOk();

        $auditoria = Audit::query()
            ->where('auditable_type', 'relatorio')
            ->where('event', 'exported')
            ->latest('id')
            ->first();

        $this->assertNotNull($auditoria, 'toda exportação precisa ficar registrada');
        $this->assertSame('health', $auditoria->new_values['relatorio']);
        $this->assertSame('pdf', $auditoria->new_values['formato']);
        $this->assertSame(1, $auditoria->new_values['registros']);
        $this->assertSame($admin->id, $auditoria->user_id);
    }

    public function test_exportacao_preserva_o_filtro_de_setor(): void
    {
        $admin = User::factory()->admin()->create();

        Incident::create([
            'user_id' => User::factory()->create(['setor' => 'Produção'])->id,
            'data_ocorrencia' => now(),
            'local' => 'Galpao Producao',
            'descricao' => 'Corte na mao',
            'severidade' => 'leve',
            'tipo' => 'acidente',
        ]);

        Incident::create([
            'user_id' => User::factory()->create(['setor' => 'Administrativo'])->id,
            'data_ocorrencia' => now(),
            'local' => 'Sala Administracao',
            'descricao' => 'Queda de cadeira',
            'severidade' => 'leve',
            'tipo' => 'incidente',
        ]);

        $auditoria = null;

        $this->actingAs($admin)
            ->get('/relatorios/incidents/excel?setor=Administrativo')
            ->assertOk();

        $auditoria = Audit::query()
            ->where('auditable_type', 'relatorio')
            ->latest('id')
            ->first();

        $this->assertSame(1, $auditoria->new_values['registros']);
        $this->assertSame('Administrativo', $auditoria->new_values['filtros']['setor']);
    }

    public function test_relatorio_nao_expoe_cpf(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['cpf' => '529.982.247-26']);

        $this->actingAs($admin)
            ->get('/relatorios/users')
            ->assertOk()
            ->assertDontSee('529.982.247-26')
            ->assertDontSee('52998224726');
    }
}
