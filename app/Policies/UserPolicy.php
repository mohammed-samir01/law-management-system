<?php

namespace App\Policies;

use App\Models\User;
use App\Policies\Concerns\HasOfficeScope;

class UserPolicy
{
    use HasOfficeScope;

    public function viewAny(User $user): bool
    {
        // Managing users is an admin task; lawyers/assistants don't see the list.
        // (super_admin is allowed via the global Gate::before bypass.)
        return $this->isOfficeAdmin($user);
    }

    public function view(User $user, User $model): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        return $user->office_id === $model->office_id;
    }

    public function create(User $user): bool
    {
        return $this->isSuperAdmin($user) || $this->isOfficeAdmin($user);
    }

    public function update(User $user, User $model): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        if ($this->isOfficeAdmin($user)) return $user->office_id === $model->office_id;
        return $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        if ($user->id === $model->id) return false;
        if ($this->isSuperAdmin($user)) return true;
        return $this->isOfficeAdmin($user) && $user->office_id === $model->office_id;
    }

    public function export(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isSuperAdmin($user);
    }
}
