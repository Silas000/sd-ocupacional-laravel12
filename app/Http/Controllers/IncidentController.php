<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\PaginatesResults;
use App\Http\Requests\IncidentRequest;
use App\Models\Incident;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    use PaginatesResults;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Incident::class);

        $filtros = $this->filtros($request, ['q', 'severidade', 'tipo', 'de', 'ate']);

        $incidents = Incident::query()
            ->with('user')
            ->when(
                ! auth()->user()->roleEnum()->hasSafetyAccess(),
                fn ($query) => $query->where('user_id', auth()->id())
            )
            ->filtrar($filtros)
            ->latest('data_ocorrencia')
            ->paginate($this->perPage($request))
            ->withQueryString();

        return view('incidents.index', [
            'incidents' => $incidents,
            'filtros' => $filtros,
            'tipos' => $this->incidentTypeOptions(),
            'severidades' => $this->incidentSeverityOptions(),
        ]);
    }

    public function create()
    {
        $this->authorize('create', Incident::class);

        return view('incidents.create', [
            'users' => $this->selectableUsers(),
            'tipos' => $this->incidentTypeOptions(),
            'severidades' => $this->incidentSeverityOptions(),
        ]);
    }

    public function store(IncidentRequest $request)
    {
        Incident::create($request->validated());

        return redirect()->route('incidents.index')->with('success', 'Ocorrência registrada com sucesso.');
    }

    public function show(Incident $incident)
    {
        $this->authorize('view', $incident);

        $incident->load('user', 'risks');

        return view('incidents.show', compact('incident'));
    }

    public function edit(Incident $incident)
    {
        $this->authorize('update', $incident);

        return view('incidents.edit', [
            'incident' => $incident,
            'users' => $this->selectableUsers(),
            'tipos' => $this->incidentTypeOptions(),
            'severidades' => $this->incidentSeverityOptions(),
        ]);
    }

    public function update(IncidentRequest $request, Incident $incident)
    {
        $incident->update($request->validated());

        return redirect()->route('incidents.index')->with('success', 'Ocorrência atualizada com sucesso.');
    }

    public function destroy(Incident $incident)
    {
        $this->authorize('delete', $incident);

        $incident->delete();

        return redirect()->route('incidents.index')->with('success', 'Ocorrência excluída com sucesso.');
    }

    public function restore(int $incident)
    {
        $registro = Incident::withTrashed()->findOrFail($incident);

        $this->authorize('restore', $registro);

        $registro->restore();

        return redirect()->route('incidents.index')->with('success', 'Ocorrência restaurada com sucesso.');
    }
}
