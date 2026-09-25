<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;

class ExamPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->roleEnum()->hasHealthAccess();
    }

    public function view(User $user, Exam $exam): bool
    {
        return $user->roleEnum()->hasHealthAccess() || $exam->user_id === $user->getKey();
    }

    public function create(User $user): bool
    {
        return $user->roleEnum()->hasHealthAccess();
    }

    public function update(User $user, Exam $exam): bool
    {
        return $user->roleEnum()->hasHealthAccess();
    }

    public function delete(User $user, Exam $exam): bool
    {
        return $user->roleEnum()->hasHealthAccess();
    }

    public function restore(User $user, Exam $exam): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Exam $exam): bool
    {
        return $user->isAdmin();
    }
}
