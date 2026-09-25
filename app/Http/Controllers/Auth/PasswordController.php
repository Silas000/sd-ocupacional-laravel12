<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Rules\NotReusedPassword;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols(),
                new NotReusedPassword($request->user()),
            ],
        ], [
            'current_password.required' => 'A senha atual é obrigatória.',
            'current_password.current_password' => 'A senha atual está incorreta.',
            'password.required' => 'A nova senha é obrigatória.',
            'password.confirmed' => 'A confirmação de senha não coincide.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.mixed' => 'A senha deve conter letras maiúsculas e minúsculas.',
            'password.numbers' => 'A senha deve conter ao menos um número.',
            'password.symbols' => 'A senha deve conter ao menos um símbolo.',
        ]);

        $request->user()->update([
            'password' => $validated['password'],
        ]);

        return back()->with('success', 'Senha atualizada com sucesso!');
    }
}
