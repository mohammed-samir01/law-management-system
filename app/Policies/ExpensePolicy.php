<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;
use App\Policies\Concerns\HasOfficeScope;

class ExpensePolicy
{
    use HasOfficeScope;

    public function viewAny(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }

    public function view(User $user, Expense $expense): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        return $this->sameOffice($user, $expense);
    }

    public function create(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }

    public function update(User $user, Expense $expense): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        if (!$this->sameOffice($user, $expense)) return false;
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }

    public function delete(User $user, Expense $expense): bool
    {
        if (!$this->sameOffice($user, $expense)) return false;
        return $this->isOfficeAdmin($user);
    }

    public function export(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }
}
