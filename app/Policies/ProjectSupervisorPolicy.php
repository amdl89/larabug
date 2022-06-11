<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectSupervisorPolicy
{
    use HandlesAuthorization;

    public function update(User $user)
    {
        return $user->hasRole(UserRole::Admin);
    }
}
