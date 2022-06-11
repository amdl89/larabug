<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectCoverImagePolicy
{
    use HandlesAuthorization;

    public function update(User $user, Project $project)
    {
        return $user->hasRole(UserRole::Admin) ||
            $project->whereHasSupervisor(
                fn ($query) => $query->whereKey($user->id)
            )->exists();
    }

    public function destroy(User $user, Project $project)
    {
        return $user->hasRole(UserRole::Admin) ||
            $project->whereHasSupervisor(
                fn ($query) => $query->whereKey($user->id)
            )->exists();
    }
}
