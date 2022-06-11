<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketAttachmentPolicy
{
    use HandlesAuthorization;

    public function store(User $user, Ticket $ticket)
    {
        return $user->hasRole(UserRole::Admin) ||
            $ticket->whereHas(
                'project',
                fn ($query) => $query->whereHasSupervisor(
                    fn ($q) => $q->whereKey($user->id)
                )
            )
            ->orWhereHas(
                'creator',
                fn ($query) => $query->whereKey($user->id)
            )
            ->exists();
    }
}
