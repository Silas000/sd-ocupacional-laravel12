<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\NotReusedPassword;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = User::where('email', $request->string('email')->value())->first();

        $passwordRules = [
            'required',
            'confirmed',
            PasswordRule::min(8)->mixedCase()->numbers()->symbols(),
        ];

        if ($user !== null) {
            $passwordRules[] = new NotReusedPassword($user);
        }

        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => $passwordRules,
        ], [
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail informado é inválido.',
            'password.required' => 'A senha é obrigatória.',
            'password.confirmed' => 'A confirmação de senha não coincide.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.mixed' => 'A senha deve conter letras maiúsculas e minúsculas.',
            'password.numbers' => 'A senha deve conter ao menos um número.',
            'password.symbols' => 'A senha deve conter ao menos um símbolo.',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => $request->string('password')->value(),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', 'Senha redefinida com sucesso!')
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => 'Não foi possível redefinir a senha. Verifique os dados informados.']);
    }
}
