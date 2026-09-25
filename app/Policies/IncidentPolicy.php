<?php

namespace App\Policies;

use App\Models\Incident;
use App\Models\User;

class IncidentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->roleEnum()->hasSafetyAccess();
    }

    public function view(User $user, Incident $incident): bool
    {
        return $user->roleEnum()->hasSafetyAccess() || $incident->user_id === $user->getKey();
    }

    public function create(User $user): bool
    {
        return $user->roleEnum()->hasSafetyAccess();
    }

    public function update(User $user, Incident $incident): bool
    {
        return $user->roleEnum()->hasSafetyAccess();
    }

    public function delete(User $user, Incident $incident): bool
    {
        return $user->roleEnum()->hasSafetyAccess();
    }

    public function restore(User $user, Incident $incident): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Incident $incident): bool
    {
        return $user->isAdmin();
    }
}
