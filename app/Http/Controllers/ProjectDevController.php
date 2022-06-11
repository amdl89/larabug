<?php

namespace App\Http\Controllers;

use App\Enums\ToastType;
use App\Enums\UserRole;
use App\Http\Resources\UserResource;
use App\Models\Project;
use App\Rules\AllDevsInArrayExist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectDevController extends Controller
{
    public function index(Project $project)
    {
        Gate::authorize('view-all-project-dev', [$project]);

        return UserResource::collection(
            $project->users()
                ->with(['profile' => fn ($q) => $q->select('id', 'name')])
                ->whereHas(
                    'roles',
                    fn ($query) => $query->where('name', UserRole::Dev)
                )
                ->select('users.id')
                ->withForeignKeys()
                ->get()
        );
    }

    public function update(Request $request, Project $project)
    {
        Gate::authorize('update-project-dev');

        $request->validate([
            'devs' => ['required', 'array', new AllDevsInArrayExist],
        ]);

        $project->syncDevs($request->get('devs'));

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Project Devs Updated Successfully!'
        ]);

        return back();
    }
}
