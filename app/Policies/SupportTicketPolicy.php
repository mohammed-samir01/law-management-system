<?php

namespace App\Policies;

use App\Models\SupportTicket;
use App\Models\User;
use App\Policies\Concerns\HasOfficeScope;

class SupportTicketPolicy
{
    use HasOfficeScope;

    public function viewAny(User $user): bool
    {
        return $this->isStaff($user);
    }

    public function view(User $user, SupportTicket $ticket): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        if (!$this->sameOffice($user, $ticket)) return false;
        // Assigned lawyer can view their tickets; admin sees all
        if ($this->isLawyer($user) || $this->isAssistant($user)) {
            return $ticket->created_by === $user->id || $ticket->assigned_to === $user->id;
        }
        return true;
    }

    public function create(User $user): bool
    {
        return auth()->check(); // any authenticated user can open a ticket
    }

    public function update(User $user, SupportTicket $ticket): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        if (!$this->sameOffice($user, $ticket)) return false;
        return $this->isOfficeAdmin($user) || $ticket->assigned_to === $user->id;
    }

    public function delete(User $user, SupportTicket $ticket): bool
    {
        if (!$this->sameOffice($user, $ticket)) return false;
        return $this->isOfficeAdmin($user);
    }

    public function export(User $user): bool
    {
        return $this->isOfficeAdmin($user) || $this->isLawyer($user);
    }
}
