<?php

namespace App\Policies;

use App\Models\PaymentGateway;
use App\Models\User;
use App\Policies\Concerns\HasOfficeScope;

class PaymentGatewayPolicy
{
    use HasOfficeScope;

    public function viewAny(User $user): bool
    {
        return $this->isOfficeAdmin($user);
    }

    public function view(User $user, PaymentGateway $gateway): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        return $this->sameOffice($user, $gateway);
    }

    public function create(User $user): bool
    {
        return $this->isOfficeAdmin($user);
    }

    public function update(User $user, PaymentGateway $gateway): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        if (!$this->sameOffice($user, $gateway)) return false;
        return $this->isOfficeAdmin($user);
    }

    public function delete(User $user, PaymentGateway $gateway): bool
    {
        if (!$this->sameOffice($user, $gateway)) return false;
        return $this->isOfficeAdmin($user);
    }

    public function export(User $user): bool
    {
        return $this->isOfficeAdmin($user);
    }
}
