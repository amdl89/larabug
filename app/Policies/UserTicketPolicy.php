<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserTicketPolicy
{
    use HandlesAuthorization;

    public function viewAll(User $user, User $routeUser)
    {
        return $user->is($routeUser) ||
            $user->hasRole(UserRole::Admin);
    }
}
