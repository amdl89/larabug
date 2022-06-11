<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AllProjectTeamPolicy
{
    use HandlesAuthorization;

    public function edit(User $user)
    {
        return $user->hasRole(UserRole::Admin);
    }
}
