<?php

namespace App\Policies\Concerns;

use App\Models\User;

trait HasOfficeScope
{
    protected function isSuperAdmin(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    protected function isOfficeAdmin(User $user): bool
    {
        return $user->hasRole('office_admin');
    }

    protected function isLawyer(User $user): bool
    {
        return $user->hasRole('lawyer');
    }

    protected function isAssistant(User $user): bool
    {
        return $user->hasRole('assistant');
    }

    protected function isStaff(User $user): bool
    {
        return $user->hasAnyRole(['office_admin', 'lawyer', 'assistant']);
    }

    protected function sameOffice(User $user, mixed $model): bool
    {
        return $user->office_id && $user->office_id === $model->office_id;
    }
}
