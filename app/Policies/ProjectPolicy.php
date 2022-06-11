<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;

    public function viewAll(User $user)
    {
        return $user->hasRole(UserRole::Admin);
    }

    public function show(User $user, Project $project)
    {
        return $user->hasRole(UserRole::Admin) ||
            $project->whereHas(
                'users',
                fn ($query) => $query->whereKey($user->id)
            )
            ->exists();
    }

    public function store(User $user)
    {
        return $user->hasRole(UserRole::Admin);
    }

    public function update(User $user, Project $project)
    {
        return $user->hasRole(UserRole::Admin) ||
            $project->whereHasSupervisor(
                fn ($query) => $query->whereKey($user->id)
            )
            ->exists();
    }

    public function destroy(User $user)
    {
        return $user->hasRole(UserRole::Admin);
    }
}
