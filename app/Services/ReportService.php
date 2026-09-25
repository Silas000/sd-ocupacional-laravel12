<?php

namespace App\Services;

use App\Models\Audit;
use App\Models\User;
use App\Support\ReportDefinition;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Monta a consulta de cada relatório e o cabeçalho de exportação.
 *
 * Reaproveita os mesmos escopos de filtro das listagens, de modo que o
 * relatório e a tela mostram exatamente o mesmo recorte de dados.
 */
class ReportService
{
    public function __construct(private readonly ReportRegistry $registry) {}

    /**
     * @return array<int, ReportDefinition>
     */
    public function disponiveis(User $usuario): array
    {
        return $this->registry->disponiveisPara($usuario->roleEnum());
    }

    public function buscarPara(User $usuario, string $slug): ?ReportDefinition
    {
        $relatorio = $this->registry->buscar($slug);

        return $relatorio?->permite($usuario->roleEnum()) ? $relatorio : null;
    }

    /**
     * Filtros aceitos pelo relatório, mais o de registros excluídos.
     *
     * @return array<string, string>
     */
    public function lerFiltros(Request $request, ReportDefinition $relatorio, User $usuario): array
    {
        $chaves = $relatorio->chavesDosFiltros();

        if ($this->podeVerExcluidos($usuario)) {
            $chaves[] = ReportRegistry::FILTRO_EXCLUIDOS;
        }

        $valores = [];

        foreach ($chaves as $chave) {
            $valor = $request->input($chave);

            if (is_string($valor) && trim($valor) !== '') {
                $valores[$chave] = trim($valor);
            }
        }

        return $valores;
    }

    public function podeVerExcluidos(User $usuario): bool
    {
        return $usuario->isAdmin();
    }

    /**
     * @param  array<string, string>  $filtros
     * @return Builder<covariant \Illuminate\Database\Eloquent\Model>
     */
    public function consulta(ReportDefinition $relatorio, array $filtros, User $usuario): Builder
    {
        $incluirExcluidos = ($filtros[ReportRegistry::FILTRO_EXCLUIDOS] ?? null) === '1'
            && $this->podeVerExcluidos($usuario);

        /** @var Builder<covariant \Illuminate\Database\Eloquent\Model> $consulta */
        $consulta = $relatorio->model::query()->with($relatorio->relacoes);

        if ($incluirExcluidos) {
            $consulta->withTrashed();
        }

        $consulta->filtrar($filtros)
            ->orderBy($relatorio->ordem, $relatorio->direcao);

        /*
         * Quem não tem acesso à área de saúde fica restrito aos próprios
         * registros, mesmo se um relatório for liberado por engano.
         */
        if (! $usuario->roleEnum()->hasHealthAccess() && in_array($relatorio->slug, ['exams', 'health'], true)) {
            $consulta->where('user_id', $usuario->getKey());
        }

        return $consulta;
    }

    /**
     * @param  array<string, string>  $filtros
     * @return Collection<int, Model>
     */
    public function paginar(ReportDefinition $relatorio, array $filtros, User $usuario, int $porPagina): LengthAwarePaginator
    {
        return $this->consulta($relatorio, $filtros, $usuario)->paginate($porPagina)->withQueryString();
    }

    /**
     * Todas as linhas do recorte, usadas nas exportações.
     *
     * @param  array<string, string>  $filtros
     * @return Collection<int, Model>
     */
    public function todas(ReportDefinition $relatorio, array $filtros, User $usuario): Collection
    {
        return $this->consulta($relatorio, $filtros, $usuario)->get();
    }

    /**
     * Converte as linhas em arrays associativos na ordem das colunas.
     *
     * @param  array<string, string>  $filtros
     * @return array<int, array<string, string>>
     */
    public function linhas(ReportDefinition $relatorio, array $filtros, User $usuario): array
    {
        return $this->dados($relatorio, $this->todas($relatorio, $filtros, $usuario));
    }

    /**
     * @param  Collection<int, Model>  $linhas
     * @return array<int, array<string, string>>
     */
    public function dados(ReportDefinition $relatorio, Collection $linhas): array
    {
        return $linhas->map(fn ($model): array => $this->linha($relatorio, $model))->all();
    }

    /**
     * @return array<string, string>
     */
    public function linha(ReportDefinition $relatorio, mixed $model): array
    {
        $valores = [];

        foreach ($relatorio->colunas as $chave => $coluna) {
            $valores[$chave] = (string) $coluna['valor']($model);
        }

        return $valores;
    }

    /**
     * Descrição dos filtros ativos, impressa no PDF e no cabeçalho do
     * Excel para dar contexto ao recorte exportado.
     *
     * @param  array<string, string>  $filtros
     * @return array<string, string>
     */
    public function descricaoDosFiltros(ReportDefinition $relatorio, array $filtros): array
    {
        $descricao = [];

        foreach ($filtros as $chave => $valor) {
            $definicao = $relatorio->filtros[$chave] ?? ['rotulo' => ucfirst($chave)];

            $texto = match ($definicao['tipo'] ?? 'texto') {
                'select' => $this->registry->opcoes($definicao['opcoes'] ?? '')[$valor] ?? $valor,
                'booleano' => $valor === '1' ? 'Sim' : 'Não',
                default => $valor,
            };

            $descricao[$definicao['rotulo']] = $texto;
        }

        return $descricao;
    }

    /**
     * Exportar dados de saúde exige justification no prontuário da
     * auditoria, então toda saída do sistema fica registrada.
     *
     * @param  array<string, string>  $filtros
     */
    public function auditarExportacao(ReportDefinition $relatorio, array $filtros, string $formato, int $total): void
    {
        Audit::create([
            'auditable_type' => 'relatorio',
            'auditable_id' => null,
            'event' => 'exported',
            'old_values' => null,
            'new_values' => [
                'relatorio' => $relatorio->slug,
                'formato' => $formato,
                'registros' => $total,
                'filtros' => $filtros,
            ],
            'user_id' => Auth::id(),
            'user_name' => Auth::user()?->name,
            'ip_address' => request()->ip(),
            'user_agent' => mb_substr((string) request()->userAgent(), 0, 255),
            'url' => mb_substr((string) request()->fullUrl(), 0, 255),
            'created_at' => now(),
        ]);
    }
}
