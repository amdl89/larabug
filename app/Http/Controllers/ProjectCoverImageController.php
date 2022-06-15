<?php

namespace App\Http\Controllers;

use App\Enums\ToastType;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectCoverImageController extends Controller
{
    public function update(Request $request, Project $project)
    {
        Gate::authorize('update-project-cover-image', [$project]);

        $request->validate([
            'coverImage' => ['required', 'file', 'image', 'max:10240'],
        ]);

        $project->addMediaFromRequest('coverImage')
            ->toMediaCollection('coverImage');

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Cover Image Updated Successfully!'
        ]);

        return back();
    }

    public function destroy(Request $request, Project $project)
    {
        Gate::authorize('destroy-project-cover-image', [$project]);

        $project->clearMediaCollection('coverImage');

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Cover Image Removed Successfully!'
        ]);

        return back();
    }
}
