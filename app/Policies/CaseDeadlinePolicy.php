<?php

namespace App\Policies;

use App\Models\CaseDeadline;
use App\Models\User;
use App\Policies\Concerns\HasOfficeScope;

class CaseDeadlinePolicy
{
    use HasOfficeScope;

    public function viewAny(User $user): bool
    {
        return $this->isStaff($user);
    }

    public function view(User $user, CaseDeadline $deadline): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        return $this->sameOffice($user, $deadline);
    }

    public function create(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }

    public function update(User $user, CaseDeadline $deadline): bool
    {
        if (! $this->sameOffice($user, $deadline)) return false;
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }

    public function delete(User $user, CaseDeadline $deadline): bool
    {
        if (! $this->sameOffice($user, $deadline)) return false;
        return $this->isOfficeAdmin($user);
    }
}
