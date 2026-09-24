<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\User;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function __construct()
    {
        $this->middleware('check.role:admin,medico');
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            $exams = Exam::with('user')->get();
        } elseif ($user->isMedico()) {
            $exams = Exam::with('user')->get();
        } else {
            $exams = Exam::where('user_id', $user->id)->with('user')->get();
        }

        return view('exams.index', compact('exams'));
    }

    public function create()
    {
        $users = User::all();
        return view('exams.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'tipo' => ['required', 'string', 'max:100'],
            'data_exame' => ['required', 'date'],
            'data_vencimento' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'max:50'],
            'medico_responsavel' => ['nullable', 'string', 'max:100'],
            'resultado' => ['nullable', 'string'],
            'observacoes' => ['nullable', 'string'],
        ], [
            'user_id.required' => 'O funcionário é obrigatório.',
            'user_id.exists' => 'O funcionário selecionado não existe.',
            'tipo.required' => 'O tipo do exame é obrigatório.',
            'tipo.string' => 'O tipo do exame deve ser um texto.',
            'tipo.max' => 'O tipo do exame não pode ter mais de 100 caracteres.',
            'data_exame.required' => 'A data do exame é obrigatória.',
            'data_exame.date' => 'A data do exame é inválida.',
            'data_vencimento.date' => 'A data de vencimento é inválida.',
            'status.string' => 'O status deve ser um texto.',
            'status.max' => 'O status não pode ter mais de 50 caracteres.',
            'medico_responsavel.string' => 'O médico responsável deve ser um texto.',
            'medico_responsavel.max' => 'O nome do médico responsável não pode ter mais de 100 caracteres.',
            'resultado.string' => 'O resultado deve ser um texto.',
            'observacoes.string' => 'As observações devem ser um texto.',
        ]);

        Exam::create($validated);

        return redirect()->route('exams.index')->with('success', 'Exame cadastrado com sucesso.');
    }

    public function show(Exam $exam)
    {
        return view('exams.show', compact('exam'));
    }

    public function edit(Exam $exam)
    {
        $users = User::all();
        return view('exams.edit', compact('exam', 'users'));
    }

    public function update(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'tipo' => ['required', 'string', 'max:100'],
            'data_exame' => ['required', 'date'],
            'data_vencimento' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'max:50'],
            'medico_responsavel' => ['nullable', 'string', 'max:100'],
            'resultado' => ['nullable', 'string'],
            'observacoes' => ['nullable', 'string'],
        ], [
            'user_id.required' => 'O funcionário é obrigatório.',
            'user_id.exists' => 'O funcionário selecionado não existe.',
            'tipo.required' => 'O tipo do exame é obrigatório.',
            'tipo.string' => 'O tipo do exame deve ser um texto.',
            'tipo.max' => 'O tipo do exame não pode ter mais de 100 caracteres.',
            'data_exame.required' => 'A data do exame é obrigatória.',
            'data_exame.date' => 'A data do exame é inválida.',
            'data_vencimento.date' => 'A data de vencimento é inválida.',
            'status.string' => 'O status deve ser um texto.',
            'status.max' => 'O status não pode ter mais de 50 caracteres.',
            'medico_responsavel.string' => 'O médico responsável deve ser um texto.',
            'medico_responsavel.max' => 'O nome do médico responsável não pode ter mais de 100 caracteres.',
            'resultado.string' => 'O resultado deve ser um texto.',
            'observacoes.string' => 'As observações devem ser um texto.',
        ]);

        $exam->update($validated);

        return redirect()->route('exams.index')->with('success', 'Exame atualizado com sucesso.');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();

        return redirect()->route('exams.index')->with('success', 'Exame excluído com sucesso.');
    }
}
