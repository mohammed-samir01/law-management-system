<?php

namespace App\Policies;

use App\Models\CommunicationLog;
use App\Models\User;
use App\Policies\Concerns\HasOfficeScope;

class CommunicationLogPolicy
{
    use HasOfficeScope;

    public function viewAny(User $user): bool
    {
        return $this->isStaff($user);
    }

    public function view(User $user, CommunicationLog $log): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        return $this->sameOffice($user, $log);
    }

    public function create(User $user): bool
    {
        return $this->isStaff($user) && ! $this->isSuperAdmin($user);
    }

    public function update(User $user, CommunicationLog $log): bool
    {
        if (! $this->sameOffice($user, $log)) return false;
        return $this->isStaff($user);
    }

    public function delete(User $user, CommunicationLog $log): bool
    {
        if (! $this->sameOffice($user, $log)) return false;
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }
}
