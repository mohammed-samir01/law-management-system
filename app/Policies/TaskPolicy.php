<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use App\Policies\Concerns\HasOfficeScope;

class TaskPolicy
{
    use HasOfficeScope;

    public function viewAny(User $user): bool
    {
        return $this->isStaff($user);
    }

    public function view(User $user, Task $task): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        return $this->sameOffice($user, $task);
    }

    public function create(User $user): bool
    {
        return $this->isStaff($user) && ! $this->isSuperAdmin($user);
    }

    public function update(User $user, Task $task): bool
    {
        if (! $this->sameOffice($user, $task)) return false;
        return $this->isStaff($user);
    }

    public function delete(User $user, Task $task): bool
    {
        if (! $this->sameOffice($user, $task)) return false;
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }
}
