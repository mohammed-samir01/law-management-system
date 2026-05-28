<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;
use App\Policies\Concerns\HasOfficeScope;

class InvoicePolicy
{
    use HasOfficeScope;

    public function viewAny(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }

    public function view(User $user, Invoice $invoice): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        return $this->sameOffice($user, $invoice);
    }

    public function create(User $user): bool
    {
        return $this->isOfficeAdmin($user);
    }

    public function update(User $user, Invoice $invoice): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        if (!$this->sameOffice($user, $invoice)) return false;
        return $this->isOfficeAdmin($user);
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        if (!$this->sameOffice($user, $invoice)) return false;
        return $this->isOfficeAdmin($user);
    }

    public function export(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }
}
