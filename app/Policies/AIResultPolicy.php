<?php

namespace App\Policies;

use App\Models\AIResult;
use App\Models\User;
use App\Policies\Concerns\HasOfficeScope;

class AIResultPolicy
{
    use HasOfficeScope;

    public function viewAny(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }

    public function view(User $user, AIResult $result): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        return $this->sameOffice($user, $result);
    }

    public function create(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }

    public function update(User $user, AIResult $result): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        if (!$this->sameOffice($user, $result)) return false;
        return $this->isOfficeAdmin($user);
    }

    public function delete(User $user, AIResult $result): bool
    {
        if (!$this->sameOffice($user, $result)) return false;
        return $this->isOfficeAdmin($user);
    }

    public function export(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }
}
