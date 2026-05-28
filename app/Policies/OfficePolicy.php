<?php

namespace App\Policies;

use App\Models\Office;
use App\Models\User;
use App\Policies\Concerns\HasOfficeScope;

class OfficePolicy
{
    use HasOfficeScope;

    public function viewAny(User $user): bool
    {
        return $this->isSuperAdmin($user) || $this->isOfficeAdmin($user);
    }

    public function view(User $user, Office $office): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        return $user->office_id === $office->id;
    }

    public function create(User $user): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function update(User $user, Office $office): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        return $this->isOfficeAdmin($user) && $user->office_id === $office->id;
    }

    public function delete(User $user, Office $office): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function export(User $user): bool
    {
        return $this->isSuperAdmin($user) || $this->isOfficeAdmin($user);
    }
}
