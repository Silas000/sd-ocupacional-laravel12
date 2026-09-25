<?php

namespace App\Policies;

use App\Models\Risk;
use App\Models\User;

class RiskPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->roleEnum()->hasSafetyAccess();
    }

    public function view(User $user, Risk $risk): bool
    {
        return $user->roleEnum()->hasSafetyAccess() || $risk->user_id === $user->getKey();
    }

    public function create(User $user): bool
    {
        return $user->roleEnum()->hasSafetyAccess();
    }

    public function update(User $user, Risk $risk): bool
    {
        return $user->roleEnum()->hasSafetyAccess();
    }

    public function delete(User $user, Risk $risk): bool
    {
        return $user->roleEnum()->hasSafetyAccess();
    }

    public function restore(User $user, Risk $risk): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Risk $risk): bool
    {
        return $user->isAdmin();
    }
}
