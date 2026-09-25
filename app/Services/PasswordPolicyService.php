<?php

namespace App\Services;

use App\Models\PasswordHistory;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PasswordPolicyService
{
    /**
     * Quantidade de senhas que permanecem bloqueadas para reuso.
     */
    public const HISTORY_LENGTH = 5;

    /**
     * Registra uma senha que passou a vigorar e descarta as mais antigas,
     * mantendo o histórico sempre no tamanho da janela permitida.
     */
    public function register(User $user, ?string $hash = null): void
    {
        $hash ??= $user->getAuthPassword();

        if (! is_string($hash) || strlen($hash) < 20) {
            return;
        }

        PasswordHistory::query()
            ->where('user_id', $user->getKey())
            ->where('password', $hash)
            ->delete();

        PasswordHistory::create([
            'user_id' => $user->getKey(),
            'password' => $hash,
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);

        $expired = PasswordHistory::query()
            ->where('user_id', $user->getKey())
            ->orderByDesc('id')
            ->skip(self::HISTORY_LENGTH)
            ->take(PHP_INT_MAX)
            ->pluck('id');

        if ($expired->isNotEmpty()) {
            PasswordHistory::query()->whereIn('id', $expired)->delete();
        }
    }

    /**
     * Verifica se a senha candidata já foi usada pelo usuário, seja em um
     * momento anterior, seja como senha vigente.
     */
    public function wasReused(User $user, string $candidate): bool
    {
        if (Hash::check($candidate, (string) $user->getAuthPassword())) {
            return true;
        }

        return PasswordHistory::query()
            ->where('user_id', $user->getKey())
            ->get()
            ->contains(fn (PasswordHistory $history) => Hash::check($candidate, $history->password));
    }

    /**
     * Interrompe a requisição quando a senha candidata reaproveita uma anterior.
     *
     * @throws ValidationException
     */
    public function ensureNotReused(User $user, string $candidate, string $attribute = 'password'): void
    {
        if ($this->wasReused($user, $candidate)) {
            throw ValidationException::withMessages([
                $attribute => 'Esta senha já foi utilizada recentemente. Escolha uma diferente.',
            ]);
        }
    }
}
