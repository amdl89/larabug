<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectAttachmentPolicy
{
    use HandlesAuthorization;

    public function store(User $user, Project $project)
    {
        return  $user->hasRole(UserRole::Admin) ||
            $project->whereHas(
                'users',
                fn ($query) => $query->whereKey($user->id)
            )->exists();
    }
}
