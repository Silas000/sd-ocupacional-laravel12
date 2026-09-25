<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, User $model): bool
    {
        return $user->isAdmin() || $user->is($model);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, User $model): bool
    {
        if (! $user->isAdmin()) {
            return false;
        }

        if ($user->is($model) && $model->isAdmin() && $this->roleChangeDemotes($model)) {
            return false;
        }

        return true;
    }

    public function delete(User $user, User $model): bool
    {
        if (! $user->isAdmin() || $user->is($model)) {
            return false;
        }

        return $this->wouldKeepAnAdmin($model);
    }

    public function restore(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, User $model): bool
    {
        return $user->isAdmin() && ! $user->is($model);
    }

    /**
     * Impede que o administrador remova o próprio acesso por engano.
     */
    private function roleChangeDemotes(User $model): bool
    {
        return $model->getOriginal('role') === UserRole::Admin->value
            && $model->role !== UserRole::Admin->value;
    }

    /**
     * Garante que reste ao menos um administrador ativo no sistema.
     */
    private function wouldKeepAnAdmin(User $model): bool
    {
        if ($model->role !== UserRole::Admin->value) {
            return true;
        }

        return User::query()
            ->where('role', UserRole::Admin->value)
            ->where($model->getKeyName(), '!=', $model->getKey())
            ->exists();
    }
}
