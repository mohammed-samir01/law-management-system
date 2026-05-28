<?php

namespace App\Policies;

use App\Models\PowerOfAttorney;
use App\Models\User;
use App\Policies\Concerns\HasOfficeScope;

class PowerOfAttorneyPolicy
{
    use HasOfficeScope;

    public function viewAny(User $user): bool
    {
        return $this->isStaff($user);
    }

    public function view(User $user, PowerOfAttorney $poa): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        return $this->sameOffice($user, $poa);
    }

    public function create(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }

    public function update(User $user, PowerOfAttorney $poa): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        if (!$this->sameOffice($user, $poa)) return false;
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }

    public function delete(User $user, PowerOfAttorney $poa): bool
    {
        if (!$this->sameOffice($user, $poa)) return false;
        return $this->isOfficeAdmin($user);
    }

    public function export(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }
}
