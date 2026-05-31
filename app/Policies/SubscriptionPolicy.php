<?php

namespace App\Policies;

use App\Models\Subscription;
use App\Models\User;
use App\Policies\Concerns\HasOfficeScope;

class SubscriptionPolicy
{
    use HasOfficeScope;

    public function viewAny(User $user): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function view(User $user, Subscription $subscription): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function update(User $user, Subscription $subscription): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function delete(User $user, Subscription $subscription): bool
    {
        return $this->isSuperAdmin($user);
    }
}
