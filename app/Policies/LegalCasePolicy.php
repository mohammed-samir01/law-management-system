<?php

namespace App\Policies;

use App\Models\LegalCase;
use App\Models\User;
use App\Policies\Concerns\HasOfficeScope;

class LegalCasePolicy
{
    use HasOfficeScope;

    public function viewAny(User $user): bool
    {
        return $this->isStaff($user);
    }

    public function view(User $user, LegalCase $case): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        if (!$this->sameOffice($user, $case)) return false;
        // Lawyers only see their assigned cases
        if ($this->isLawyer($user)) {
            return $case->lawyers()->where('users.id', $user->id)->exists();
        }
        return true;
    }

    public function create(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }

    public function update(User $user, LegalCase $case): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        if (!$this->sameOffice($user, $case)) return false;
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }

    public function delete(User $user, LegalCase $case): bool
    {
        if (!$this->sameOffice($user, $case)) return false;
        return $this->isOfficeAdmin($user);
    }

    public function export(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }
}
