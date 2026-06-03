<?php

namespace App\Policies;

use App\Models\TimeEntry;
use App\Models\User;
use App\Policies\Concerns\HasOfficeScope;

class TimeEntryPolicy
{
    use HasOfficeScope;

    public function viewAny(User $user): bool
    {
        return $this->isStaff($user);
    }

    public function view(User $user, TimeEntry $entry): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        return $this->sameOffice($user, $entry);
    }

    public function create(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }

    public function update(User $user, TimeEntry $entry): bool
    {
        if (! $this->sameOffice($user, $entry)) return false;
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }

    public function delete(User $user, TimeEntry $entry): bool
    {
        if (! $this->sameOffice($user, $entry)) return false;
        return $this->isOfficeAdmin($user);
    }
}
