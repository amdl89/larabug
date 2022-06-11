<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttachmentPolicy
{
    use HandlesAuthorization;

    public function destroy(User $user, Attachment $attachment)
    {
        return $user->hasRole(UserRole::Admin) ||
            $attachment->uploader()->whereKey($user->id)->exists();
    }
}
