<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProfileResource;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class UserProfileController extends Controller
{
    public function show(User $user)
    {
        Gate::authorize('show-user-profile');

        $profile = $user->profile()->with(['user.roles', 'media'])->first();

        return inertia('Users/Profiles/Show', [
            'profile' => fn () => new ProfileResource($profile),

            'can' => fn () => [
                'update-profile' => Gate::allows('update-profile', [$profile]),

                'update-profile-avatar' => Gate::allows('update-profile-avatar', [$profile]),

                'destroy-profile-avatar' => Gate::allows('destroy-profile-avatar', [$profile]),
            ],
        ]);
    }
}
