<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\User;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    public function __construct()
    {
        $this->middleware('check.role:admin,tecnico');
    }

    public function index()
    {
        $incidents = Incident::with('user')->get();
        return view('incidents.index', compact('incidents'));
    }

    public function create()
    {
        $users = User::all();
        return view('incidents.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'data_ocorrencia' => ['required', 'date'],
            'local' => ['required', 'string', 'max:255'],
            'descricao' => ['required', 'string'],
            'severidade' => ['nullable', 'string', 'max:50'],
            'tipo' => ['nullable', 'string', 'max:100'],
            'medidas_corretivas' => ['nullable', 'string'],
            'observacoes' => ['nullable', 'string'],
        ], [
            'user_id.required' => 'O funcionário é obrigatório.',
            'user_id.exists' => 'O funcionário selecionado não existe.',
            'data_ocorrencia.required' => 'A data da ocorrência é obrigatória.',
            'data_ocorrencia.date' => 'A data da ocorrência é inválida.',
            'local.required' => 'O local é obrigatório.',
            'local.string' => 'O local deve ser um texto.',
            'local.max' => 'O local não pode ter mais de 255 caracteres.',
            'descricao.required' => 'A descrição é obrigatória.',
            'descricao.string' => 'A descrição deve ser um texto.',
            'severidade.string' => 'A severidade deve ser um texto.',
            'severidade.max' => 'A severidade não pode ter mais de 50 caracteres.',
            'tipo.string' => 'O tipo deve ser um texto.',
            'tipo.max' => 'O tipo não pode ter mais de 100 caracteres.',
            'medidas_corretivas.string' => 'As medidas corretivas devem ser um texto.',
            'observacoes.string' => 'As observações devem ser um texto.',
        ]);

        Incident::create($validated);

        return redirect()->route('incidents.index')->with('success', 'Ocorrência registrada com sucesso.');
    }

    public function show(Incident $incident)
    {
        return view('incidents.show', compact('incident'));
    }

    public function edit(Incident $incident)
    {
        $users = User::all();
        return view('incidents.edit', compact('incident', 'users'));
    }

    public function update(Request $request, Incident $incident)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'data_ocorrencia' => ['required', 'date'],
            'local' => ['required', 'string', 'max:255'],
            'descricao' => ['required', 'string'],
            'severidade' => ['nullable', 'string', 'max:50'],
            'tipo' => ['nullable', 'string', 'max:100'],
            'medidas_corretivas' => ['nullable', 'string'],
            'observacoes' => ['nullable', 'string'],
        ], [
            'user_id.required' => 'O funcionário é obrigatório.',
            'user_id.exists' => 'O funcionário selecionado não existe.',
            'data_ocorrencia.required' => 'A data da ocorrência é obrigatória.',
            'data_ocorrencia.date' => 'A data da ocorrência é inválida.',
            'local.required' => 'O local é obrigatório.',
            'local.string' => 'O local deve ser um texto.',
            'local.max' => 'O local não pode ter mais de 255 caracteres.',
            'descricao.required' => 'A descrição é obrigatória.',
            'descricao.string' => 'A descrição deve ser um texto.',
            'severidade.string' => 'A severidade deve ser um texto.',
            'severidade.max' => 'A severidade não pode ter mais de 50 caracteres.',
            'tipo.string' => 'O tipo deve ser um texto.',
            'tipo.max' => 'O tipo não pode ter mais de 100 caracteres.',
            'medidas_corretivas.string' => 'As medidas corretivas devem ser um texto.',
            'observacoes.string' => 'As observações devem ser um texto.',
        ]);

        $incident->update($validated);

        return redirect()->route('incidents.index')->with('success', 'Ocorrência atualizada com sucesso.');
    }

    public function destroy(Incident $incident)
    {
        $incident->delete();

        return redirect()->route('incidents.index')->with('success', 'Ocorrência excluída com sucesso.');
    }
}
