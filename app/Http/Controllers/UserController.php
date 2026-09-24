<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('check.role:admin');
    }

    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', Rule::in(['admin', 'medico', 'tecnico', 'funcionario'])],
            'cpf' => ['nullable', 'string', 'max:20'],
            'cargo' => ['nullable', 'string', 'max:100'],
            'setor' => ['nullable', 'string', 'max:100'],
            'data_admissao' => ['nullable', 'date'],
            'data_demissao' => ['nullable', 'date'],
            'observacoes' => ['nullable', 'string'],
        ], [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail informado é inválido.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'password.required' => 'A senha é obrigatória.',
            'password.confirmed' => 'A confirmação de senha não coincide.',
            'role.required' => 'O perfil de acesso é obrigatório.',
            'role.in' => 'O perfil de acesso selecionado é inválido.',
            'data_admissao.date' => 'A data de admissão é inválida.',
            'data_demissao.date' => 'A data de demissão é inválida.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'cpf' => $request->cpf,
            'cargo' => $request->cargo,
            'setor' => $request->setor,
            'data_admissao' => $request->data_admissao,
            'data_demissao' => $request->data_demissao,
            'observacoes' => $request->observacoes,
        ]);

        return redirect()->route('users.index')->with('success', 'Usuário criado com sucesso.');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', Rule::in(['admin', 'medico', 'tecnico', 'funcionario'])],
            'cpf' => ['nullable', 'string', 'max:20'],
            'cargo' => ['nullable', 'string', 'max:100'],
            'setor' => ['nullable', 'string', 'max:100'],
            'data_admissao' => ['nullable', 'date'],
            'data_demissao' => ['nullable', 'date'],
            'observacoes' => ['nullable', 'string'],
        ], [
            'name.required' => 'O nome é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail informado é inválido.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'password.confirmed' => 'A confirmação de senha não coincide.',
            'role.required' => 'O perfil de acesso é obrigatório.',
            'role.in' => 'O perfil de acesso selecionado é inválido.',
            'data_admissao.date' => 'A data de admissão é inválida.',
            'data_demissao.date' => 'A data de demissão é inválida.',
        ]);

        $data = $request->only([
            'name', 'email', 'role', 'cpf', 'cargo', 'setor', 'data_admissao', 'data_demissao', 'observacoes'
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuário excluído com sucesso.');
    }
}
