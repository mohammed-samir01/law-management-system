<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use App\Policies\Concerns\HasOfficeScope;

class DocumentPolicy
{
    use HasOfficeScope;

    public function viewAny(User $user): bool
    {
        return $this->isStaff($user);
    }

    public function view(User $user, Document $document): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        return $this->sameOffice($user, $document);
    }

    public function create(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user) || $this->isAssistant($user);
    }

    public function update(User $user, Document $document): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        if (!$this->sameOffice($user, $document)) return false;
        return $this->isOfficeAdmin($user) || $this->isLawyer($user) || $this->isAssistant($user);
    }

    public function delete(User $user, Document $document): bool
    {
        if (!$this->sameOffice($user, $document)) return false;
        return $this->isOfficeAdmin($user);
    }

    public function export(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }
}
