<?php

namespace App\Policies;

use App\Models\HealthRecord;
use App\Models\User;

class HealthRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->roleEnum()->hasHealthAccess();
    }

    public function view(User $user, HealthRecord $healthRecord): bool
    {
        return $user->roleEnum()->hasHealthAccess() || $healthRecord->user_id === $user->getKey();
    }

    public function create(User $user): bool
    {
        return $user->roleEnum()->hasHealthAccess();
    }

    public function update(User $user, HealthRecord $healthRecord): bool
    {
        return $user->roleEnum()->hasHealthAccess();
    }

    public function delete(User $user, HealthRecord $healthRecord): bool
    {
        return $user->roleEnum()->hasHealthAccess();
    }

    public function restore(User $user, HealthRecord $healthRecord): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, HealthRecord $healthRecord): bool
    {
        return $user->isAdmin();
    }
}
