<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketAssigneePolicy
{
    use HandlesAuthorization;

    public function store(User $user, Ticket $ticket)
    {
        return Ticket::query()
            ->canBeAssignedBy($user)
            ->whereKey($ticket->id)
            ->exists();
    }
}
