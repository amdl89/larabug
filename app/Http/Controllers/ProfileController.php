<?php

namespace App\Http\Controllers;

use App\Enums\ToastType;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Profile;
use Illuminate\Support\Facades\Gate;

class ProfileController extends Controller
{
    public function update(UpdateProfileRequest $request, Profile $profile)
    {
        Gate::authorize('update-profile', [$profile]);

        $profile->update(
            $request->validated()
        );

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Profile Updated Successfully!'
        ]);

        return back();
    }
}
