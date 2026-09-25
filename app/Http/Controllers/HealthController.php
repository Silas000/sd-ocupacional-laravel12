<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\PaginatesResults;
use App\Http\Requests\HealthRecordRequest;
use App\Models\Exam;
use App\Models\HealthRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class HealthController extends Controller
{
    use PaginatesResults;

    public function index(Request $request)
    {
        $this->authorize('viewAny', HealthRecord::class);

        $filtros = $this->filtros($request, ['q', 'tipo', 'de', 'ate']);

        $records = HealthRecord::query()
            ->with('user', 'exam')
            ->when(
                ! auth()->user()->roleEnum()->hasHealthAccess(),
                fn (Builder $query) => $query->where('user_id', auth()->id())
            )
            ->filtrar($filtros)
            ->latest('id')
            ->paginate($this->perPage($request))
            ->withQueryString();

        return view('health.index', [
            'records' => $records,
            'filtros' => $filtros,
        ]);
    }

    public function create()
    {
        $this->authorize('create', HealthRecord::class);

        return view('health.create', [
            'users' => $this->selectableUsers(),
            'exams' => $this->selectableExams(),
        ]);
    }

    public function store(HealthRecordRequest $request)
    {
        HealthRecord::create($request->validated());

        return redirect()->route('health.index')->with('success', 'Registro de saúde cadastrado com sucesso.');
    }

    public function show(HealthRecord $health)
    {
        $this->authorize('view', $health);

        $health->load('user', 'exam');

        return view('health.show', compact('health'));
    }

    public function edit(HealthRecord $health)
    {
        $this->authorize('update', $health);

        return view('health.edit', [
            'health' => $health,
            'users' => $this->selectableUsers(),
            'exams' => $this->selectableExams(),
        ]);
    }

    public function update(HealthRecordRequest $request, HealthRecord $health)
    {
        $health->update($request->validated());

        return redirect()->route('health.index')->with('success', 'Registro de saúde atualizado com sucesso.');
    }

    public function destroy(HealthRecord $health)
    {
        $this->authorize('delete', $health);

        $health->delete();

        return redirect()->route('health.index')->with('success', 'Registro de saúde excluído com sucesso.');
    }

    public function restore(int $health)
    {
        $registro = HealthRecord::withTrashed()->findOrFail($health);

        $this->authorize('restore', $registro);

        $registro->restore();

        return redirect()->route('health.index')->with('success', 'Registro de saúde restaurado com sucesso.');
    }

    /**
     * @return Collection<int, Exam>
     */
    protected function selectableExams()
    {
        return Exam::query()
            ->with('user')
            ->latest('data_exame')
            ->limit(300)
            ->get(['id', 'tipo', 'data_exame', 'status', 'user_id'])
            ->sortBy(fn (Exam $exam) => $exam->user?->name ?? '')
            ->values();
    }
}
