<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\PaginatesResults;
use App\Models\User;
use App\Services\AppSettings;
use App\Services\ReportExporter;
use App\Services\ReportRegistry;
use App\Services\ReportService;
use App\Support\ReportDefinition;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    use PaginatesResults;

    public function __construct(
        private readonly ReportService $relatorios,
        private readonly ReportRegistry $catalogo,
        private readonly AppSettings $settings,
    ) {}

    public function index(Request $request): View
    {
        return view('reports.index', [
            'disponiveis' => $this->relatorios->disponiveis($request->user()),
        ]);
    }

    public function show(Request $request, string $relatorio): View
    {
        $definicao = $this->definicao($request->user(), $relatorio);
        $usuario = $request->user();

        $filtros = $this->relatorios->lerFiltros($request, $definicao, $usuario);

        $linhas = $this->relatorios->paginar($definicao, $filtros, $usuario, $this->perPage($request, 25));

        return view('reports.show', [
            'relatorio' => $definicao,
            'filtros' => $filtros,
            'linhas' => $linhas,
            'dados' => $this->relatorios->dados($definicao, new Collection($linhas->items())),
            'opcoes' => $this->opcoes($definicao),
            'resumoFiltros' => $this->relatorios->descricaoDosFiltros($definicao, $filtros),
            'podeVerExcluidos' => $this->relatorios->podeVerExcluidos($usuario),
        ]);
    }

    public function exportarPdf(Request $request, string $relatorio, ReportExporter $exportador): Response
    {
        $definicao = $this->definicao($request->user(), $relatorio);

        return $exportador->pdf(
            $definicao,
            ...$this->prepararExportacao($request, $definicao, 'pdf')
        );
    }

    public function exportarExcel(Request $request, string $relatorio, ReportExporter $exportador): BinaryFileResponse
    {
        $definicao = $this->definicao($request->user(), $relatorio);

        return $exportador->excel(
            $definicao,
            ...$this->prepararExportacao($request, $definicao, 'xlsx')
        );
    }

    /**
     * Monta linhas e metadados da exportação, registrando a auditoria.
     *
     * @return array{0: array<int, array<string, string>>, 1: array<string, string>}
     */
    private function prepararExportacao(Request $request, ReportDefinition $definicao, string $formato): array
    {
        $usuario = $request->user();

        $filtros = $this->relatorios->lerFiltros($request, $definicao, $usuario);
        $dados = $this->relatorios->linhas($definicao, $filtros, $usuario);

        $this->relatorios->auditarExportacao($definicao, $filtros, $formato, count($dados));

        $aplicados = $this->relatorios->descricaoDosFiltros($definicao, $filtros);
        $quantidade = count($dados);

        $meta = [
            'sistema' => $this->settings->nome(),
            'gerado_em' => 'Gerado em '.now()->format('d/m/Y').' às '.now()->format('H:i'),
            'gerado_por' => 'Emitido por '.$usuario->name,
            'total' => $quantidade.($quantidade === 1 ? ' registro' : ' registros'),
            'filtros' => $aplicados === []
                ? 'Sem filtros aplicados'
                : 'Filtros: '.implode(' | ', array_map(
                    static fn (string $rotulo, string $valor): string => $rotulo.': '.$valor,
                    array_keys($aplicados),
                    $aplicados
                )),
        ];

        return [$dados, $meta];
    }

    /**
     * Relatório inexistente ou fora do papel do usuário devolve 404, e
     * não 403, para não confirmar a existência de um recurso proibido.
     */
    private function definicao(User $usuario, string $slug): ReportDefinition
    {
        $definicao = $this->relatorios->buscarPara($usuario, $slug);

        abort_if($definicao === null, 404);

        return $definicao;
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function opcoes(ReportDefinition $definicao): array
    {
        $opcoes = [];

        foreach ($definicao->filtros as $chave => $filtro) {
            if (($filtro['tipo'] ?? '') === 'select') {
                $opcoes[$chave] = $this->catalogo->opcoes($filtro['opcoes'] ?? '');
            }
        }

        return $opcoes;
    }
}
