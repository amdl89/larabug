<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectDevPolicy
{
    use HandlesAuthorization;

    public function viewAll(User $user, Project $project)
    {
        return $user->hasRole(UserRole::Admin) ||
            $project->whereHasSupervisor(
                fn ($q) => $q->whereKey($user->id)
            )
            ->exists();
    }

    public function update(User $user)
    {
        return $user->hasRole(UserRole::Admin);
    }
}
