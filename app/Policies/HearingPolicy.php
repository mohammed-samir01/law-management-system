<?php

namespace App\Policies;

use App\Models\Hearing;
use App\Models\User;
use App\Policies\Concerns\HasOfficeScope;

class HearingPolicy
{
    use HasOfficeScope;

    public function viewAny(User $user): bool
    {
        return $this->isStaff($user);
    }

    public function view(User $user, Hearing $hearing): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        return $this->sameOffice($user, $hearing);
    }

    public function create(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }

    public function update(User $user, Hearing $hearing): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        if (!$this->sameOffice($user, $hearing)) return false;
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }

    public function delete(User $user, Hearing $hearing): bool
    {
        if (!$this->sameOffice($user, $hearing)) return false;
        return $this->isOfficeAdmin($user);
    }

    public function export(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }
}
