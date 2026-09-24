<?php

namespace App\Http\Controllers;

use App\Models\Risk;
use App\Models\User;
use Illuminate\Http\Request;

class RiskController extends Controller
{
    public function __construct()
    {
        $this->middleware('check.role:admin,tecnico');
    }

    public function index()
    {
        $risks = Risk::with('user')->get();
        return view('risks.index', compact('risks'));
    }

    public function create()
    {
        $users = User::all();
        return view('risks.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'setor' => ['nullable', 'string', 'max:100'],
            'nome' => ['required', 'string', 'max:100'],
            'descricao' => ['nullable', 'string'],
            'severidade' => ['nullable', 'string', 'max:50'],
            'categoria' => ['nullable', 'string', 'max:100'],
            'medidas_preventivas' => ['nullable', 'string'],
            'ativo' => ['boolean'],
        ], [
            'user_id.required' => 'O funcionário é obrigatório.',
            'user_id.exists' => 'O funcionário selecionado não existe.',
            'nome.required' => 'O nome do risco é obrigatório.',
            'nome.string' => 'O nome do risco deve ser um texto.',
            'nome.max' => 'O nome do risco não pode ter mais de 100 caracteres.',
            'descricao.string' => 'A descrição deve ser um texto.',
            'severidade.string' => 'A severidade deve ser um texto.',
            'severidade.max' => 'A severidade não pode ter mais de 50 caracteres.',
            'categoria.string' => 'A categoria deve ser um texto.',
            'categoria.max' => 'A categoria não pode ter mais de 100 caracteres.',
            'medidas_preventivas.string' => 'As medidas preventivas devem ser um texto.',
            'ativo.boolean' => 'O campo ativo deve ser verdadeiro ou falso.',
        ]);

        Risk::create($validated);

        return redirect()->route('risks.index')->with('success', 'Risco cadastrado com sucesso.');
    }

    public function show(Risk $risk)
    {
        return view('risks.show', compact('risk'));
    }

    public function edit(Risk $risk)
    {
        $users = User::all();
        return view('risks.edit', compact('risk', 'users'));
    }

    public function update(Request $request, Risk $risk)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'setor' => ['nullable', 'string', 'max:100'],
            'nome' => ['required', 'string', 'max:100'],
            'descricao' => ['nullable', 'string'],
            'severidade' => ['nullable', 'string', 'max:50'],
            'categoria' => ['nullable', 'string', 'max:100'],
            'medidas_preventivas' => ['nullable', 'string'],
            'ativo' => ['boolean'],
        ], [
            'user_id.required' => 'O funcionário é obrigatório.',
            'user_id.exists' => 'O funcionário selecionado não existe.',
            'nome.required' => 'O nome do risco é obrigatório.',
            'nome.string' => 'O nome do risco deve ser um texto.',
            'nome.max' => 'O nome do risco não pode ter mais de 100 caracteres.',
            'descricao.string' => 'A descrição deve ser um texto.',
            'severidade.string' => 'A severidade deve ser um texto.',
            'severidade.max' => 'A severidade não pode ter mais de 50 caracteres.',
            'categoria.string' => 'A categoria deve ser um texto.',
            'categoria.max' => 'A categoria não pode ter mais de 100 caracteres.',
            'medidas_preventivas.string' => 'As medidas preventivas devem ser um texto.',
            'ativo.boolean' => 'O campo ativo deve ser verdadeiro ou falso.',
        ]);

        $risk->update($validated);

        return redirect()->route('risks.index')->with('success', 'Risco atualizado com sucesso.');
    }

    public function destroy(Risk $risk)
    {
        $risk->delete();

        return redirect()->route('risks.index')->with('success', 'Risco excluído com sucesso.');
    }
}
