<?php

namespace App\Rules;

use App\Models\User;
use App\Support\Cpf;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueCpf implements ValidationRule
{
    public function __construct(private readonly ?int $ignoreUserId = null) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $fingerprint = Cpf::fingerprint(is_string($value) ? $value : null);

        if ($fingerprint === null) {
            return;
        }

        $query = User::withTrashed()->where('cpf_hash', $fingerprint);

        if ($this->ignoreUserId !== null) {
            $query->where('id', '!=', $this->ignoreUserId);
        }

        if ($query->exists()) {
            $fail('Já existe um usuário cadastrado com este CPF.');
        }
    }
}
