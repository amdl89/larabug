<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\UserResource;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AllProjectTeamController extends Controller
{
    public function edit(Request $request)
    {
        Gate::authorize('edit-all-project-team');

        $request->validate([
            'initialProject' => 'exists:projects,id',
        ]);

        return inertia('AllProjects/Teams/Edit', [
            'projects' => fn () => ProjectResource::collection(
                Project::query()
                    ->withSupervisor()
                    ->with([
                        'supervisor' => fn ($q1) => $q1->with(
                            ['profile' => fn ($q2) => $q2->select('id', 'name')]
                        )
                            ->select('users.id')
                            ->withForeignKeys(),
                        'devs' => fn ($q1) => $q1->with(
                            ['profile' => fn ($q2) => $q2->select('id', 'name')]
                        )
                            ->select('users.id')
                            ->withForeignKeys(),
                        'testers' => fn ($q1) => $q1->with(
                            ['profile' => fn ($q2) => $q2->select('id', 'name')]
                        )
                            ->select('users.id')
                            ->withForeignKeys(),
                    ])
                    ->get()
            ),

            'supervisors' => fn () => UserResource::collection(
                User::query()
                    ->with(['profile' => fn ($q) => $q->select('id', 'name')])
                    ->canSuperviseAProject()
                    ->select('id')
                    ->withForeignKeys()
                    ->get()
            ),

            'devs' => fn () => UserResource::collection(
                User::query()
                    ->with(['profile' => fn ($q) => $q->select('id', 'name')])
                    ->whereRole(UserRole::Dev)
                    ->select('id')
                    ->withForeignKeys()
                    ->get()
            ),

            'testers' => fn () => UserResource::collection(
                User::query()
                    ->with(['profile' => fn ($q) => $q->select('id', 'name')])
                    ->whereRole(UserRole::Tester)
                    ->select('id')
                    ->withForeignKeys()
                    ->get()
            ),
        ]);
    }
}
