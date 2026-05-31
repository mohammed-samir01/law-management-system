<?php

namespace App\Policies;

use App\Models\PlatformLead;
use App\Models\User;
use App\Policies\Concerns\HasOfficeScope;

class PlatformLeadPolicy
{
    use HasOfficeScope;

    public function viewAny(User $user): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function view(User $user, PlatformLead $lead): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, PlatformLead $lead): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function delete(User $user, PlatformLead $lead): bool
    {
        return $this->isSuperAdmin($user);
    }
}
