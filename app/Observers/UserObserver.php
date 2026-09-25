<?php

namespace App\Observers;

use App\Models\User;
use App\Services\PasswordPolicyService;

class UserObserver
{
    public function __construct(private readonly PasswordPolicyService $passwords) {}

    /**
     * A senha inicial entra no histórico para não poder ser reaproveitada
     * logo na sequência. No evento 'created' os originais ainda não foram
     * sincronizados, então usamos o atributo já com o cast aplicado.
     */
    public function created(User $user): void
    {
        $this->passwords->register($user, $user->getAuthPassword());
    }

    /**
     * Toda senha que passa a valer, venha de onde vier (perfil, troca
     * obrigatória, reset por e-mail ou troca feita por administrador),
     * entra no histórico e passa a ser bloqueada para reuso.
     */
    public function updated(User $user): void
    {
        if (! $user->wasChanged('password')) {
            return;
        }

        $this->passwords->register($user, $user->getAuthPassword());
    }
}
