<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserAssignedTicketPolicy
{
    use HandlesAuthorization;

    public function viewAll(User $user, User $routeUser)
    {
        return User::query()
            ->canBeAssignedTickets()
            ->whereKey($routeUser->id)
            ->exists()
            && ($routeUser->is($user) || $user->hasRole(UserRole::Admin));
    }
}
