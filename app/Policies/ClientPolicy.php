<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;
use App\Policies\Concerns\HasOfficeScope;

class ClientPolicy
{
    use HasOfficeScope;

    public function viewAny(User $user): bool
    {
        return $this->isStaff($user);
    }

    public function view(User $user, Client $client): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        return $this->sameOffice($user, $client);
    }

    public function create(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }

    public function update(User $user, Client $client): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        if (!$this->sameOffice($user, $client)) return false;
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }

    public function delete(User $user, Client $client): bool
    {
        if (!$this->sameOffice($user, $client)) return false;
        return $this->isOfficeAdmin($user);
    }

    public function export(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }
}
