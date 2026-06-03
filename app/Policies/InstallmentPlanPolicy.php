<?php

namespace App\Policies;

use App\Models\InstallmentPlan;
use App\Models\User;
use App\Policies\Concerns\HasOfficeScope;

class InstallmentPlanPolicy
{
    use HasOfficeScope;

    public function viewAny(User $user): bool
    {
        return $this->isStaff($user);
    }

    public function view(User $user, InstallmentPlan $plan): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        return $this->sameOffice($user, $plan);
    }

    public function create(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }

    public function update(User $user, InstallmentPlan $plan): bool
    {
        if (! $this->sameOffice($user, $plan)) return false;
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }

    public function delete(User $user, InstallmentPlan $plan): bool
    {
        if (! $this->sameOffice($user, $plan)) return false;
        return $this->isOfficeAdmin($user);
    }
}
