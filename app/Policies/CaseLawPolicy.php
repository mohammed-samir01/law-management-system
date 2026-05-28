<?php

namespace App\Policies;

use App\Models\CaseLaw;
use App\Models\User;
use App\Policies\Concerns\HasOfficeScope;

class CaseLawPolicy
{
    use HasOfficeScope;

    public function viewAny(User $user): bool
    {
        return $this->isStaff($user);
    }

    public function view(User $user, CaseLaw $caseLaw): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        return $this->sameOffice($user, $caseLaw);
    }

    public function create(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }

    public function update(User $user, CaseLaw $caseLaw): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        if (!$this->sameOffice($user, $caseLaw)) return false;
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }

    public function delete(User $user, CaseLaw $caseLaw): bool
    {
        if (!$this->sameOffice($user, $caseLaw)) return false;
        return $this->isOfficeAdmin($user);
    }

    public function export(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }
}
