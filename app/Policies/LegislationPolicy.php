<?php

namespace App\Policies;

use App\Models\Legislation;
use App\Models\User;
use App\Policies\Concerns\HasOfficeScope;

class LegislationPolicy
{
    use HasOfficeScope;

    public function viewAny(User $user): bool
    {
        return $this->isStaff($user);
    }

    public function view(User $user, Legislation $legislation): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        return $this->sameOffice($user, $legislation);
    }

    public function create(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }

    public function update(User $user, Legislation $legislation): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        if (!$this->sameOffice($user, $legislation)) return false;
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }

    public function delete(User $user, Legislation $legislation): bool
    {
        if (!$this->sameOffice($user, $legislation)) return false;
        return $this->isOfficeAdmin($user);
    }

    public function export(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }
}
