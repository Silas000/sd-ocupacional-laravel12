<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\PaginatesResults;
use App\Http\Requests\RiskRequest;
use App\Models\Risk;
use Illuminate\Http\Request;

class RiskController extends Controller
{
    use PaginatesResults;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Risk::class);

        $filtros = $this->filtros($request, ['q', 'severidade', 'categoria', 'setor', 'ativo']);

        $risks = Risk::query()
            ->with('user')
            ->when(
                ! auth()->user()->roleEnum()->hasSafetyAccess(),
                fn ($query) => $query->where('user_id', auth()->id())
            )
            ->filtrar($filtros)
            ->latest('id')
            ->paginate($this->perPage($request))
            ->withQueryString();

        return view('risks.index', [
            'risks' => $risks,
            'filtros' => $filtros,
            'severidades' => $this->riskSeverityOptions(),
            'categorias' => $this->riskCategoryOptions(),
            'setores' => $this->setoresExistentes(),
        ]);
    }

    public function create()
    {
        $this->authorize('create', Risk::class);

        return view('risks.create', [
            'users' => $this->selectableUsers(),
            'severidades' => $this->riskSeverityOptions(),
            'categorias' => $this->riskCategoryOptions(),
        ]);
    }

    public function store(RiskRequest $request)
    {
        Risk::create($request->validated());

        return redirect()->route('risks.index')->with('success', 'Risco cadastrado com sucesso.');
    }

    public function show(Risk $risk)
    {
        $this->authorize('view', $risk);

        $risk->load('user', 'incidents');

        return view('risks.show', compact('risk'));
    }

    public function edit(Risk $risk)
    {
        $this->authorize('update', $risk);

        return view('risks.edit', [
            'risk' => $risk,
            'users' => $this->selectableUsers(),
            'severidades' => $this->riskSeverityOptions(),
            'categorias' => $this->riskCategoryOptions(),
        ]);
    }

    public function update(RiskRequest $request, Risk $risk)
    {
        $risk->update($request->validated());

        return redirect()->route('risks.index')->with('success', 'Risco atualizado com sucesso.');
    }

    public function destroy(Risk $risk)
    {
        $this->authorize('delete', $risk);

        $risk->delete();

        return redirect()->route('risks.index')->with('success', 'Risco excluído com sucesso.');
    }

    public function restore(int $risk)
    {
        $registro = Risk::withTrashed()->findOrFail($risk);

        $this->authorize('restore', $registro);

        $registro->restore();

        return redirect()->route('risks.index')->with('success', 'Risco restaurado com sucesso.');
    }
}
