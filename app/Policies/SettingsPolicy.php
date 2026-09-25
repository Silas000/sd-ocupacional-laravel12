<?php

namespace App\Policies;

use App\Models\User;

/**
 * A configuração visual é global e afeta todos os usuários, então só o
 * administrador a enxerga. A tela também fica atrás do middleware de
 * papel; a policy fecha o acesso no nível do controller.
 */
class SettingsPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user): bool
    {
        return $user->isAdmin();
    }
}
