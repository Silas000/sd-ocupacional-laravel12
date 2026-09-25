<?php

namespace App\Rules;

use App\Support\Cpf;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidCpf implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        if (! Cpf::isValid(is_string($value) ? $value : null)) {
            $fail('O CPF informado é inválido.');
        }
    }
}
