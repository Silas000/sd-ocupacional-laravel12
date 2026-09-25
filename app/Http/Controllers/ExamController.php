<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\PaginatesResults;
use App\Http\Requests\ExamRequest;
use App\Models\Exam;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    use PaginatesResults;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Exam::class);

        $filtros = $this->filtros($request, ['q', 'status', 'tipo', 'setor', 'de', 'ate']);

        $exams = Exam::query()
            ->with('user')
            ->when(
                ! auth()->user()->roleEnum()->hasHealthAccess(),
                fn (Builder $query) => $query->doUsuario(auth()->id())
            )
            ->filtrar($filtros)
            ->latest('id')
            ->paginate($this->perPage($request))
            ->withQueryString();

        return view('exams.index', [
            'exams' => $exams,
            'filtros' => $filtros,
            'statuses' => $this->examStatusOptions(),
            'tipos' => $this->examTypeOptions(),
            'setores' => $this->setoresExistentes(),
        ]);
    }

    public function create()
    {
        $this->authorize('create', Exam::class);

        return view('exams.create', [
            'users' => $this->selectableUsers(),
            'tipos' => $this->examTypeOptions(),
            'statuses' => $this->examStatusOptions(),
        ]);
    }

    public function store(ExamRequest $request)
    {
        Exam::create($request->validated());

        return redirect()->route('exams.index')->with('success', 'Exame cadastrado com sucesso.');
    }

    public function show(Exam $exam)
    {
        $this->authorize('view', $exam);

        $exam->load('user', 'healthRecords');

        return view('exams.show', compact('exam'));
    }

    public function edit(Exam $exam)
    {
        $this->authorize('update', $exam);

        return view('exams.edit', [
            'exam' => $exam,
            'users' => $this->selectableUsers(),
            'tipos' => $this->examTypeOptions(),
            'statuses' => $this->examStatusOptions(),
        ]);
    }

    public function update(ExamRequest $request, Exam $exam)
    {
        $exam->update($request->validated());

        return redirect()->route('exams.index')->with('success', 'Exame atualizado com sucesso.');
    }

    public function destroy(Exam $exam)
    {
        $this->authorize('delete', $exam);

        $exam->delete();

        return redirect()->route('exams.index')->with('success', 'Exame excluído com sucesso.');
    }

    public function restore(int $exam)
    {
        $registro = Exam::withTrashed()->findOrFail($exam);

        $this->authorize('restore', $registro);

        $registro->restore();

        return redirect()->route('exams.index')->with('success', 'Exame restaurado com sucesso.');
    }
}
