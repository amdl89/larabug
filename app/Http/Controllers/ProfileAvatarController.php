<?php

namespace App\Http\Controllers;

use App\Enums\ToastType;
use App\Models\Profile;
use Gate;
use Illuminate\Http\Request;

class ProfileAvatarController extends Controller
{
    public function update(Request $request, Profile $profile)
    {
        Gate::authorize('update-profile-avatar', [$profile]);

        $request->validate([
            'avatar' => ['required', 'file', 'image', 'max:20480'],
        ]);

        $profile->addMediaFromRequest('avatar')
            ->toMediaCollection('avatar');

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Avatar Updated Successfully!'
        ]);

        return back();
    }

    public function destroy(Request $request, Profile $profile)
    {
        Gate::authorize('destroy-profile-avatar', [$profile]);

        $profile->clearMediaCollection('avatar');

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Avatar Removed Successfully!'
        ]);

        return back();
    }
}
