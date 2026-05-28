<?php

namespace App\Policies;

use App\Models\EnforcementFile;
use App\Models\User;
use App\Policies\Concerns\HasOfficeScope;

class EnforcementFilePolicy
{
    use HasOfficeScope;

    public function viewAny(User $user): bool
    {
        return $this->isStaff($user);
    }

    public function view(User $user, EnforcementFile $file): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        return $this->sameOffice($user, $file);
    }

    public function create(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }

    public function update(User $user, EnforcementFile $file): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        if (!$this->sameOffice($user, $file)) return false;
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }

    public function delete(User $user, EnforcementFile $file): bool
    {
        if (!$this->sameOffice($user, $file)) return false;
        return $this->isOfficeAdmin($user);
    }

    public function export(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }
}
