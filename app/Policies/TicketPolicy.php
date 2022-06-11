<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketPolicy
{
    use HandlesAuthorization;

    public function viewAll(User $user)
    {
        return $user->hasRole(UserRole::Admin);
    }

    public function store(User $user, Project $project)
    {
        return $user->hasRole(UserRole::Admin) ||
            $project->whereHas(
                'users',
                fn ($query) => $query->whereKey($user->id)
            )->exists();
    }

    public function show(User $user, Ticket $ticket)
    {
        return Ticket::query()
            ->canBeViewedBy($user)
            ->whereKey($ticket->id)
            ->exists();
    }

    public function update(User $user, Ticket $ticket)
    {
        return Ticket::query()
            ->canBeModifiedBy($user)
            ->whereKey($ticket->id)
            ->exists();
    }

    public function destroy(User $user, Ticket $ticket)
    {
        return Ticket::query()
            ->canBeDeletedBy($user)
            ->whereKey($ticket->id)
            ->exists();
    }
}
