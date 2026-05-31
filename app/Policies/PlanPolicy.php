<?php

namespace App\Policies;

use App\Models\Plan;
use App\Models\User;
use App\Policies\Concerns\HasOfficeScope;

class PlanPolicy
{
    use HasOfficeScope;

    public function viewAny(User $user): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function view(User $user, Plan $plan): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function update(User $user, Plan $plan): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function delete(User $user, Plan $plan): bool
    {
        return $this->isSuperAdmin($user);
    }
}
