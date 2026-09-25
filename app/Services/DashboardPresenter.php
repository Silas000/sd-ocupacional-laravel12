<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\Incident;
use App\Models\Risk;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Monta os dados de cada dashboard. Concentra as regras de leitura e as
 * consultas de métrica para que os controllers fiquem apenas com a
 * escolha da view.
 */
class DashboardPresenter
{
    public function __construct(private readonly User $viewer) {}

    public function admin(): array
    {
        return [
            'usersCount' => User::count(),
            'examsCount' => Exam::count(),
            'risksCount' => Risk::count(),
            'incidentsCount' => Incident::count(),
            'recentExams' => Exam::with('user')->latest('id')->limit(5)->get(),
            'recentIncidents' => Incident::with('user')->latest('data_ocorrencia')->limit(5)->get(),
            'examsByStatus' => $this->totalPorColuna(Exam::query(), 'status'),
            'risksBySeverity' => $this->totalPorColuna(Risk::query()->whereNotNull('severidade'), 'severidade'),
            'examsVencidos' => Exam::vencidos()->count(),
        ];
    }

    public function medico(): array
    {
        $setor = $this->viewer->setor;

        return [
            'examesPendentes' => Exam::pendentes()->count(),
            'examesVencidos' => Exam::vencidos()->count(),
            'examesRecentes' => Exam::with('user')
                ->when($setor !== null, fn ($query) => $query->doSetorDoUsuario($setor))
                ->latest('data_exame')
                ->limit(10)
                ->get(),
            'risksBySetor' => Risk::porSetorComTotais()->pluck('total', 'setor'),
        ];
    }

    public function tecnico(): array
    {
        return [
            'risksCount' => Risk::count(),
            'incidentsCount' => Incident::count(),
            'risksBySetor' => Risk::porSetorComTotais()->pluck('total', 'setor'),
            'risksBySeverity' => $this->totalPorColuna(Risk::query()->whereNotNull('severidade'), 'severidade'),
            'incidentsByMonth' => $this->ocorrencasPorMes(now()->year),
            'recentIncidents' => Incident::with('user')->latest('data_ocorrencia')->limit(5)->get(),
        ];
    }

    public function funcionario(): array
    {
        return [
            'examesPessoais' => Exam::with('user')
                ->doUsuario($this->viewer->getKey())
                ->latest('data_exame')
                ->get(),
            'examesVencidos' => Exam::vencidos()->doUsuario($this->viewer->getKey())->count(),
            'risksSetor' => $this->risksDoSetorDoColaborador(),
            'incidentsPessoais' => Incident::with('user')
                ->where('user_id', $this->viewer->getKey())
                ->latest('data_ocorrencia')
                ->limit(10)
                ->get(),
        ];
    }

    /**
     * Sem setor definido, o colaborador não tem visão de riscos.
     *
     * @return EloquentCollection<int, Risk>
     */
    private function risksDoSetorDoColaborador(): EloquentCollection
    {
        if ($this->viewer->setor === null) {
            return new EloquentCollection;
        }

        return Risk::doSetor($this->viewer->setor)->latest('id')->get();
    }

    /**
     * Contagem por valor de uma coluna, ignorando nulos.
     *
     * @param  Builder<covariant \Illuminate\Database\Eloquent\Model>  $query
     * @return Collection<int|string, int>
     */
    private function totalPorColuna(Builder $query, string $coluna): Collection
    {
        return $query
            ->selectRaw("{$coluna} as valor, COUNT(*) as total")
            ->whereNotNull($coluna)
            ->groupBy('valor')
            ->orderBy('valor')
            ->pluck('total', 'valor');
    }

    /**
     * Contagem de ocorrências mês a mês, com extração de mês compatível
     * com MySQL, PostgreSQL e SQLite. Sempre devolve os 12 meses, mesmo
     * os que não tiveram registros.
     *
     * @return array<int, int>
     */
    private function ocorrencasPorMes(int $ano): array
    {
        $coluna = 'data_ocorrencia';

        $expressaoMes = match (DB::connection()->getDriverName()) {
            'mysql', 'mariadb' => "MONTH({$coluna})",
            'pgsql' => "EXTRACT(MONTH FROM {$coluna})",
            default => "CAST(strftime('%m', {$coluna}) AS INTEGER)",
        };

        $contagem = Incident::query()
            ->selectRaw("{$expressaoMes} as mes, COUNT(*) as total")
            ->whereYear($coluna, $ano)
            ->groupBy('mes')
            ->pluck('total', 'mes');

        $resultado = [];

        foreach (range(1, 12) as $mes) {
            $resultado[$mes] = (int) ($contagem[$mes] ?? 0);
        }

        return $resultado;
    }
}
