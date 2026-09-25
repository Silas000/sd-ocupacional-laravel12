<?php

namespace App\Rules;

use App\Models\User;
use App\Services\PasswordPolicyService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NotReusedPassword implements ValidationRule
{
    public function __construct(private readonly User $user) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || $value === '') {
            return;
        }

        if (app(PasswordPolicyService::class)->wasReused($this->user, $value)) {
            $fail('Esta senha já foi utilizada recentemente. Escolha uma diferente.');
        }
    }
}
