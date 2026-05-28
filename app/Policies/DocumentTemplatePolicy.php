<?php

namespace App\Policies;

use App\Models\DocumentTemplate;
use App\Models\User;
use App\Policies\Concerns\HasOfficeScope;

class DocumentTemplatePolicy
{
    use HasOfficeScope;

    public function viewAny(User $user): bool
    {
        return $this->isStaff($user);
    }

    public function view(User $user, DocumentTemplate $template): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        return $this->sameOffice($user, $template);
    }

    public function create(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }

    public function update(User $user, DocumentTemplate $template): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        if (!$this->sameOffice($user, $template)) return false;
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }

    public function delete(User $user, DocumentTemplate $template): bool
    {
        if (!$this->sameOffice($user, $template)) return false;
        return $this->isOfficeAdmin($user);
    }

    public function export(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }
}
