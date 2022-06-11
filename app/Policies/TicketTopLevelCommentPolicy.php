<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketTopLevelCommentPolicy
{
    use HandlesAuthorization;

    public function viewAll(User $user, Ticket $ticket)
    {
        return $user->hasRole(UserRole::Admin) ||
            $ticket->whereHas(
                'project.users',
                fn ($query) => $query->whereKey($user->id)
            )->exists();
    }
}
