<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'cpf' => ['nullable', 'string', 'max:20'],
            'cargo' => ['nullable', 'string', 'max:100'],
            'setor' => ['nullable', 'string', 'max:100'],
            'data_admissao' => ['nullable', 'date'],
            'observacoes' => ['nullable', 'string'],
        ], [
            'name.required' => 'O nome é obrigatório.',
            'name.string' => 'O nome deve ser um texto.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.string' => 'O e-mail deve ser um texto.',
            'email.lowercase' => 'O e-mail deve estar em letras minúsculas.',
            'email.email' => 'O e-mail informado é inválido.',
            'email.max' => 'O e-mail não pode ter mais de 255 caracteres.',
            'email.unique' => 'Este e-mail já está cadastro. Use outro ou faça login.',
            'password.required' => 'A senha é obrigatória.',
            'password.confirmed' => 'A confirmação de senha não coincide.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'cpf.max' => 'O CPF não pode ter mais de 20 caracteres.',
            'cargo.max' => 'O cargo não pode ter mais de 100 caracteres.',
            'setor.max' => 'O setor não pode ter mais de 100 caracteres.',
            'data_admissao.date' => 'A data de admissão é inválida.',
            'observacoes.string' => 'As observações devem ser um texto.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'funcionario',
            'cpf' => $request->cpf,
            'cargo' => $request->cargo,
            'setor' => $request->setor,
            'data_admissao' => $request->data_admissao,
            'observacoes' => $request->observacoes,
            'must_change_password' => true,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('force-password-change')->with('success', 'Conta criada com sucesso! Altere sua senha inicial.');
    }
}