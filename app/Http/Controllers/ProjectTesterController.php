<?php

namespace App\Http\Controllers;

use App\Enums\ToastType;
use App\Models\Project;
use App\Rules\AllTestersInArrayExist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectTesterController extends Controller
{
    public function update(Request $request, Project $project)
    {
        Gate::authorize('update-project-tester');

        $request->validate([
            'testers' => ['required', 'array', new AllTestersInArrayExist],
        ]);

        $project->syncTesters($request->get('testers'));

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Project Testers Updated Successfully!'
        ]);

        return back();
    }
}
