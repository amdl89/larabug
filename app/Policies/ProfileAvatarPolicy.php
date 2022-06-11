<?php

namespace App\Policies;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProfileAvatarPolicy
{
    use HandlesAuthorization;

    public function update(User $user, Profile $profile)
    {
        return $user->profile()->whereKey($profile->id)->exists();
    }

    public function destroy(User $user, Profile $profile)
    {
        return $user->profile()->whereKey($profile->id)->exists();
    }
}
