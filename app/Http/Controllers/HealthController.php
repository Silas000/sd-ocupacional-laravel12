<?php

namespace App\Http\Controllers;

use App\Models\HealthRecord;
use App\Models\User;
use App\Models\Exam;
use Illuminate\Http\Request;

class HealthController extends Controller
{
    public function __construct()
    {
        $this->middleware('check.role:admin,medico');
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $records = HealthRecord::with('user', 'exam')->get();
        } elseif ($user->isMedico()) {
            $records = HealthRecord::with('user', 'exam')->get();
        } else {
            $records = HealthRecord::where('user_id', $user->id)->with('user', 'exam')->get();
        }

        return view('health.index', compact('records'));
    }

    public function create()
    {
        $users = User::all();
        $exams = Exam::all();
        return view('health.create', compact('users', 'exams'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'exam_id' => ['nullable', 'exists:exams,id'],
            'data_registro' => ['required', 'date'],
            'descricao' => ['required', 'string'],
            'tipo' => ['required', 'string', 'max:100'],
            'observacoes' => ['nullable', 'string'],
        ], [
            'user_id.required' => 'O funcionário é obrigatório.',
            'user_id.exists' => 'O funcionário selecionado não existe.',
            'exam_id.exists' => 'O exame selecionado não existe.',
            'data_registro.required' => 'A data do registro é obrigatória.',
            'data_registro.date' => 'A data do registro é inválida.',
            'descricao.required' => 'A descrição é obrigatória.',
            'descricao.string' => 'A descrição deve ser um texto.',
            'tipo.required' => 'O tipo é obrigatório.',
            'tipo.string' => 'O tipo deve ser um texto.',
            'tipo.max' => 'O tipo não pode ter mais de 100 caracteres.',
            'observacoes.string' => 'As observações devem ser um texto.',
        ]);

        HealthRecord::create($validated);

        return redirect()->route('health.index')->with('success', 'Registro de saúde cadastrado com sucesso.');
    }

    public function show(HealthRecord $health)
    {
        return view('health.show', compact('health'));
    }

    public function edit(HealthRecord $health)
    {
        $users = User::all();
        $exams = Exam::all();
        return view('health.edit', compact('health', 'users', 'exams'));
    }

    public function update(Request $request, HealthRecord $health)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'exam_id' => ['nullable', 'exists:exams,id'],
            'data_registro' => ['required', 'date'],
            'descricao' => ['required', 'string'],
            'tipo' => ['required', 'string', 'max:100'],
            'observacoes' => ['nullable', 'string'],
        ], [
            'user_id.required' => 'O funcionário é obrigatório.',
            'user_id.exists' => 'O funcionário selecionado não existe.',
            'exam_id.exists' => 'O exame selecionado não existe.',
            'data_registro.required' => 'A data do registro é obrigatória.',
            'data_registro.date' => 'A data do registro é inválida.',
            'descricao.required' => 'A descrição é obrigatória.',
            'descricao.string' => 'A descrição deve ser um texto.',
            'tipo.required' => 'O tipo é obrigatório.',
            'tipo.string' => 'O tipo deve ser um texto.',
            'tipo.max' => 'O tipo não pode ter mais de 100 caracteres.',
            'observacoes.string' => 'As observações devem ser um texto.',
        ]);

        $health->update($validated);

        return redirect()->route('health.index')->with('success', 'Registro de saúde atualizado com sucesso.');
    }

    public function destroy(HealthRecord $health)
    {
        $health->delete();

        return redirect()->route('health.index')->with('success', 'Registro de saúde excluído com sucesso.');
    }
}
