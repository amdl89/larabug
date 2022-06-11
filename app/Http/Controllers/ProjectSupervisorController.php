<?php

namespace App\Http\Controllers;

use App\Enums\ToastType;
use App\Models\Project;
use App\Rules\SupervisorWithIdExists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectSupervisorController extends Controller
{
    public function store(Request $request, Project $project)
    {
        Gate::authorize('update-project-supervisor');

        $request->validate([
            'supervisor' => ['nullable', new SupervisorWithIdExists],
        ]);

        $project->associateSupervisor($request->get('supervisor'));

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Supervisor Assigned Successfully!'
        ]);

        return back();
    }
}
