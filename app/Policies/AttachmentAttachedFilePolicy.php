<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Attachment;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttachmentAttachedFilePolicy
{
    use HandlesAuthorization;

    public function show(User $user, Attachment $attachment)
    {
        if ($user->hasRole(UserRole::Admin))
            return true;

        $attachable = $attachment->attachable;

        switch (get_class($attachable))
        {
            case Project::class:
                return $attachable->users()->whereKey($user->id)->exists();

            case Ticket::class:
                return $attachable->whereHas(
                    'project.users',
                    fn ($query) => $query->whereKey($user->id)
                )->exists();
        }
    }
}
