<?php

namespace App\Services;

use App\Enums\ExamStatus;
use App\Enums\ExamType;
use App\Enums\IncidentSeverity;
use App\Enums\IncidentType;
use App\Enums\RiskCategory;
use App\Enums\RiskSeverity;
use App\Enums\UserRole;
use App\Models\Exam;
use App\Models\HealthRecord;
use App\Models\Incident;
use App\Models\Risk;
use App\Models\User;
use App\Support\ReportDefinition;
use DateTimeInterface;
use Illuminate\Support\Str;

/**
 * Catálogo de relatórios disponíveis no sistema.
 *
 * Cada relatório declara quais papéis o acessam e quais filtros aceita.
 * As permissões espelham as das listagens: quem não enxerga a área de
 * saúde também não gera relatório dela.
 */
class ReportRegistry
{
    public const FILTRO_EXCLUIDOS = 'incluir_excluidos';

    /**
     * Placeholder usado quando uma célula não tem conteúdo.
     */
    public const VAZIO = '—';

    /**
     * @return array<string, ReportDefinition>
     */
    public function todos(): array
    {
        return [
            'exams' => $this->exames(),
            'health' => $this->prontuarios(),
            'risks' => $this->riscos(),
            'incidents' => $this->ocorrencias(),
            'users' => $this->usuarios(),
        ];
    }

    public function buscar(string $slug): ?ReportDefinition
    {
        return $this->todos()[$slug] ?? null;
    }

    /**
     * Relatórios que o papel informado pode abrir.
     *
     * @return array<int, ReportDefinition>
     */
    public function disponiveisPara(UserRole $papel): array
    {
        return array_values(array_filter(
            $this->todos(),
            static fn (ReportDefinition $relatorio): bool => $relatorio->permite($papel)
        ));
    }

    /**
     * Opções de um filtro `select`, resolvidas em tempo de execução
     * quando dependem de dados que já existem no banco.
     *
     * @return array<string, string>
     */
    public function opcoes(string $referencia): array
    {
        return match ($referencia) {
            'exames_status' => $this->opcoesDeEnum(ExamStatus::cases()),
            'exames_tipo' => $this->opcoesDeEnum(ExamType::cases()),
            'riscos_severidade' => $this->opcoesDeEnum(RiskSeverity::cases()),
            'riscos_categoria' => $this->opcoesDeEnum(RiskCategory::cases()),
            'ocorrencias_tipo' => $this->opcoesDeEnum(IncidentType::cases()),
            'ocorrencias_severidade' => $this->opcoesDeEnum(IncidentSeverity::cases()),
            'papeis' => $this->opcoesDeEnum(UserRole::cases()),
            'situacao' => ['ativos' => 'Ativos', 'demitidos' => 'Demitidos'],
            'setores' => $this->opcoesDeSetores(),
            'prontuarios_tipo' => $this->opcoesDeProntuario(),
            default => [],
        };
    }

    private function exames(): ReportDefinition
    {
        return new ReportDefinition(
            slug: 'exams',
            titulo: 'Exames',
            descricao: 'Exames ocupacionais com situação, validade e responsável técnico.',
            icone: 'clipboard',
            papeis: [UserRole::Admin, UserRole::Medico],
            model: Exam::class,
            relacoes: ['user'],
            filtros: [
                'q' => ['rotulo' => 'Buscar', 'tipo' => 'texto'],
                'status' => ['rotulo' => 'Status', 'tipo' => 'select', 'opcoes' => 'exames_status'],
                'tipo' => ['rotulo' => 'Tipo', 'tipo' => 'select', 'opcoes' => 'exames_tipo'],
                'setor' => ['rotulo' => 'Setor', 'tipo' => 'select', 'opcoes' => 'setores'],
                'de' => ['rotulo' => 'Realizados de', 'tipo' => 'data'],
                'ate' => ['rotulo' => 'Realizados até', 'tipo' => 'data'],
            ],
            colunas: [
                'paciente' => ['rotulo' => 'Paciente', 'valor' => fn (Exam $linha): string => $linha->user?->name ?? self::VAZIO],
                'setor' => ['rotulo' => 'Setor', 'valor' => fn (Exam $linha): string => $linha->user?->setor ?? self::VAZIO],
                'tipo' => ['rotulo' => 'Tipo', 'valor' => fn (Exam $linha): string => $linha->tipoEnum()?->label() ?? $linha->tipo],
                'data_exame' => ['rotulo' => 'Data do exame', 'valor' => fn (Exam $linha): string => self::data($linha->data_exame)],
                'vencimento' => ['rotulo' => 'Vencimento', 'valor' => fn (Exam $linha): string => self::data($linha->data_vencimento)],
                'status' => ['rotulo' => 'Situação', 'valor' => fn (Exam $linha): string => $linha->statusEnum()?->label() ?? $linha->status],
                'medico' => ['rotulo' => 'Médico responsável', 'valor' => fn (Exam $linha): string => self::texto($linha->medico_responsavel)],
            ],
            ordem: 'data_exame',
        );
    }

    private function prontuarios(): ReportDefinition
    {
        return new ReportDefinition(
            slug: 'health',
            titulo: 'Histórico de saúde',
            descricao: 'Prontuários com tipo, descrição e exame de origem.',
            icone: 'pulseira',
            papeis: [UserRole::Admin, UserRole::Medico],
            model: HealthRecord::class,
            relacoes: ['user', 'exam'],
            filtros: [
                'q' => ['rotulo' => 'Buscar', 'tipo' => 'texto'],
                'tipo' => ['rotulo' => 'Tipo', 'tipo' => 'select', 'opcoes' => 'prontuarios_tipo'],
                'de' => ['rotulo' => 'Registros de', 'tipo' => 'data'],
                'ate' => ['rotulo' => 'Registros até', 'tipo' => 'data'],
            ],
            colunas: [
                'paciente' => ['rotulo' => 'Paciente', 'valor' => fn (HealthRecord $linha): string => $linha->user?->name ?? self::VAZIO],
                'setor' => ['rotulo' => 'Setor', 'valor' => fn (HealthRecord $linha): string => $linha->user?->setor ?? self::VAZIO],
                'data' => ['rotulo' => 'Data', 'valor' => fn (HealthRecord $linha): string => self::data($linha->data_registro)],
                'tipo' => ['rotulo' => 'Tipo', 'valor' => fn (HealthRecord $linha): string => self::texto($linha->tipo, 40)],
                'descricao' => ['rotulo' => 'Descrição', 'valor' => fn (HealthRecord $linha): string => self::texto($linha->descricao, 90)],
                'exame' => ['rotulo' => 'Exame vinculado', 'valor' => fn (HealthRecord $linha): string => $linha->exam_id === null
                    ? self::VAZIO
                    : '#'.$linha->exam_id],
            ],
            ordem: 'data_registro',
        );
    }

    private function riscos(): ReportDefinition
    {
        return new ReportDefinition(
            slug: 'risks',
            titulo: 'Riscos',
            descricao: 'Riscos ocupacionais por setor, severidade e categoria.',
            icone: 'triangulo',
            papeis: [UserRole::Admin, UserRole::Tecnico],
            model: Risk::class,
            relacoes: ['user'],
            filtros: [
                'q' => ['rotulo' => 'Buscar', 'tipo' => 'texto'],
                'severidade' => ['rotulo' => 'Severidade', 'tipo' => 'select', 'opcoes' => 'riscos_severidade'],
                'categoria' => ['rotulo' => 'Categoria', 'tipo' => 'select', 'opcoes' => 'riscos_categoria'],
                'setor' => ['rotulo' => 'Setor', 'tipo' => 'select', 'opcoes' => 'setores'],
                'ativo' => ['rotulo' => 'Ativos', 'tipo' => 'booleano'],
            ],
            colunas: [
                'risco' => ['rotulo' => 'Risco', 'valor' => fn (Risk $linha): string => self::texto($linha->nome, 60)],
                'setor' => ['rotulo' => 'Setor', 'valor' => fn (Risk $linha): string => self::texto($linha->setor)],
                'severidade' => ['rotulo' => 'Severidade', 'valor' => fn (Risk $linha): string => $linha->severidadeEnum()?->label() ?? $linha->severidade],
                'categoria' => ['rotulo' => 'Categoria', 'valor' => fn (Risk $linha): string => $linha->categoriaEnum()?->label() ?? $linha->categoria],
                'situacao' => ['rotulo' => 'Situação', 'valor' => fn (Risk $linha): string => $linha->ativo ? 'Ativo' : 'Inativo'],
                'responsavel' => ['rotulo' => 'Responsável', 'valor' => fn (Risk $linha): string => $linha->user?->name ?? self::VAZIO],
                'medidas' => ['rotulo' => 'Medidas preventivas', 'valor' => fn (Risk $linha): string => self::texto($linha->medidas_preventivas, 70)],
            ],
            ordem: 'nome',
            direcao: 'asc',
        );
    }

    private function ocorrencias(): ReportDefinition
    {
        return new ReportDefinition(
            slug: 'incidents',
            titulo: 'Ocorrências',
            descricao: 'Acidentes, incidentes e quase acidentes registrados.',
            icone: 'escudo',
            papeis: [UserRole::Admin, UserRole::Tecnico],
            model: Incident::class,
            relacoes: ['user'],
            filtros: [
                'q' => ['rotulo' => 'Buscar', 'tipo' => 'texto'],
                'tipo' => ['rotulo' => 'Tipo', 'tipo' => 'select', 'opcoes' => 'ocorrencias_tipo'],
                'severidade' => ['rotulo' => 'Severidade', 'tipo' => 'select', 'opcoes' => 'ocorrencias_severidade'],
                'setor' => ['rotulo' => 'Setor', 'tipo' => 'select', 'opcoes' => 'setores'],
                'de' => ['rotulo' => 'Ocorridas de', 'tipo' => 'data'],
                'ate' => ['rotulo' => 'Ocorridas até', 'tipo' => 'data'],
            ],
            colunas: [
                'data' => ['rotulo' => 'Data', 'valor' => fn (Incident $linha): string => self::data($linha->data_ocorrencia)],
                'tipo' => ['rotulo' => 'Tipo', 'valor' => fn (Incident $linha): string => $linha->tipoEnum()?->label() ?? $linha->tipo],
                'severidade' => ['rotulo' => 'Severidade', 'valor' => fn (Incident $linha): string => $linha->severidadeEnum()?->label() ?? $linha->severidade],
                'local' => ['rotulo' => 'Local', 'valor' => fn (Incident $linha): string => self::texto($linha->local, 40)],
                'descricao' => ['rotulo' => 'Descrição', 'valor' => fn (Incident $linha): string => self::texto($linha->descricao, 90)],
                'vitima' => ['rotulo' => 'Vítima', 'valor' => fn (Incident $linha): string => $linha->user?->name ?? self::VAZIO],
            ],
            ordem: 'data_ocorrencia',
        );
    }

    private function usuarios(): ReportDefinition
    {
        return new ReportDefinition(
            slug: 'users',
            titulo: 'Colaboradores',
            descricao: 'Cadastro de colaboradores por papel, setor e situação.',
            icone: 'usuarios',
            papeis: [UserRole::Admin],
            model: User::class,
            relacoes: [],
            filtros: [
                'q' => ['rotulo' => 'Buscar', 'tipo' => 'texto'],
                'role' => ['rotulo' => 'Papel', 'tipo' => 'select', 'opcoes' => 'papeis'],
                'setor' => ['rotulo' => 'Setor', 'tipo' => 'select', 'opcoes' => 'setores'],
                'situacao' => ['rotulo' => 'Situação', 'tipo' => 'select', 'opcoes' => 'situacao'],
            ],
            colunas: [
                'nome' => ['rotulo' => 'Nome', 'valor' => fn (User $linha): string => $linha->name],
                'email' => ['rotulo' => 'E-mail', 'valor' => fn (User $linha): string => $linha->email],
                'cargo' => ['rotulo' => 'Cargo', 'valor' => fn (User $linha): string => self::texto($linha->cargo, 40)],
                'setor' => ['rotulo' => 'Setor', 'valor' => fn (User $linha): string => self::texto($linha->setor)],
                'papel' => ['rotulo' => 'Papel', 'valor' => fn (User $linha): string => $linha->roleEnum()->label()],
                'situacao' => ['rotulo' => 'Situação', 'valor' => fn (User $linha): string => $linha->isDemitido() ? 'Demitido' : 'Ativo'],
                'admissao' => ['rotulo' => 'Admissão', 'valor' => fn (User $linha): string => self::data($linha->data_admissao)],
            ],
            ordem: 'name',
            direcao: 'asc',
        );
    }

    /**
     * @param  array<int, \BackedEnum>  $casos
     * @return array<string, string>
     */
    private function opcoesDeEnum(array $casos): array
    {
        $opcoes = [];

        foreach ($casos as $caso) {
            $opcoes[(string) $caso->value] = $caso->label();
        }

        return $opcoes;
    }

    /**
     * @return array<string, string>
     */
    private function opcoesDeSetores(): array
    {
        return User::query()
            ->whereNotNull('setor')
            ->where('setor', '<>', '')
            ->distinct()
            ->orderBy('setor')
            ->pluck('setor', 'setor')
            ->all();
    }

    /**
     * O tipo do prontuário é texto livre no cadastro, então as opções
     * são os valores já registrados.
     *
     * @return array<string, string>
     */
    private function opcoesDeProntuario(): array
    {
        return HealthRecord::query()
            ->whereNotNull('tipo')
            ->where('tipo', '<>', '')
            ->distinct()
            ->orderBy('tipo')
            ->pluck('tipo', 'tipo')
            ->all();
    }

    public static function data(?DateTimeInterface $data): string
    {
        return $data instanceof DateTimeInterface ? $data->format('d/m/Y') : self::VAZIO;
    }

    public static function texto(mixed $valor, int $limite = 40): string
    {
        $texto = trim((string) $valor);

        return $texto === '' ? self::VAZIO : Str::limit($texto, $limite);
    }
}
