<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;
use App\Policies\Concerns\HasOfficeScope;

class PaymentPolicy
{
    use HasOfficeScope;

    public function viewAny(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }

    public function view(User $user, Payment $payment): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        return $this->sameOffice($user, $payment);
    }

    public function create(User $user): bool
    {
        return $this->isOfficeAdmin($user);
    }

    public function update(User $user, Payment $payment): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        if (!$this->sameOffice($user, $payment)) return false;
        return $this->isOfficeAdmin($user);
    }

    public function delete(User $user, Payment $payment): bool
    {
        if (!$this->sameOffice($user, $payment)) return false;
        return $this->isOfficeAdmin($user);
    }

    public function export(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }
}
