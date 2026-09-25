<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use App\Models\User;
use App\Rules\NotReusedPassword;
use App\Rules\UniqueCpf;
use App\Rules\ValidCpf;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends BaseRequest
{
    public function authorize(): bool
    {
        $target = $this->route('user');

        return $target instanceof User
            ? $this->user()->can('update', $target)
            : $this->user()->can('create', User::class);
    }

    public function rules(): array
    {
        $target = $this->route('user');
        $targetId = $target instanceof User ? $target->getKey() : null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($targetId)],
            'password' => $this->passwordRules($target),
            'role' => ['required', Rule::enum(UserRole::class)],
            'cpf' => ['nullable', 'string', 'max:20', new ValidCpf, new UniqueCpf($targetId)],
            'cargo' => ['nullable', 'string', 'max:100'],
            'setor' => ['nullable', 'string', 'max:100'],
            'data_admissao' => ['nullable', 'date'],
            'data_demissao' => ['nullable', 'date', 'after_or_equal:data_admissao'],
            'observacoes' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<int, mixed>
     */
    protected function passwordRules(?User $target): array
    {
        $strength = Password::min(8)->mixedCase()->numbers()->symbols();

        $rules = $target instanceof User
            ? ['nullable', 'confirmed', $strength, new NotReusedPassword($target)]
            : ['required', 'confirmed', $strength];

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    protected function attributeNames(): array
    {
        return [
            'name' => 'nome',
            'role' => 'perfil de acesso',
            'cpf' => 'CPF',
            'cargo' => 'cargo',
            'setor' => 'setor',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'role.in' => 'O perfil de acesso selecionado é inválido.',
            'data_demissao.after_or_equal' => 'A data de demissão deve ser igual ou posterior à data de admissão.',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.mixed' => 'A senha deve conter letras maiúsculas e minúsculas.',
            'password.numbers' => 'A senha deve conter ao menos um número.',
            'password.symbols' => 'A senha deve conter ao menos um símbolo.',
        ];
    }
}
