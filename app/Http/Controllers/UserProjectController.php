<?php

namespace App\Http\Controllers;

use App\Enums\ProjectStatus;
use App\Http\Requests\FilterProjectsRequest;
use App\Http\Resources\ProjectPriorityResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\UserResource;
use App\Models\Project;
use App\Models\ProjectPriority;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class UserProjectController extends Controller
{
    public function index(FilterProjectsRequest $request, User $user)
    {
        Gate::authorize('view-all-user-project', [$user]);

        return inertia('Users/Projects/Index', [
            'totalProjectCount' => fn () => $user->projects()->count(),

            'activeProjectCount' => fn () => $user->projects()
                ->where('status', ProjectStatus::Active)
                ->count(),

            'overdueProjectCount' => fn () => $user->projects()
                ->whereOverDue()
                ->count(),

            'projects' => fn () => ProjectResource::collection(
                $user->projects()
                    ->withSupervisor()
                    ->with([
                        'priority' => fn ($q) => $q->select('id', 'name', 'color'),
                        'supervisor' => fn ($q1) => $q1->with(
                            ['profile' => fn ($q2) => $q2->select('id', 'name')]
                        )
                            ->select('id')
                            ->withForeignKeys(),
                    ])
                    ->when(
                        $request->get('searchQuery'),
                        function ($query, $searchQuery) use ($request)
                        {
                            $request->get('sortBy') ?
                                $query->search($searchQuery) :
                                $query->searchAndOrder($searchQuery);
                        }
                    )
                    ->when(
                        $request->get('filters'),
                        fn ($query, $filters) => $query->filter($filters)
                    )
                    ->when(
                        $request->get('sortBy'),
                        fn ($query, $sortBy) => $query->sort($sortBy),
                        fn ($query) => $query->orderBy('id', 'desc')
                    )
                    ->addSelect([
                        'can-view' => Project::from('projects', 'p')
                            ->canBeViewedBy($request->user())
                            ->whereColumn('projects.id', 'p.id')
                            ->select('id')
                            ->limit(1),
                        'can-edit' =>  Project::from('projects', 'p')
                            ->canBeModifiedBy($request->user())
                            ->whereColumn('projects.id', 'p.id')
                            ->select('id')
                            ->limit(1),
                        'can-delete' =>  Project::from('projects', 'p')
                            ->canBeDeletedBy($request->user())
                            ->whereColumn('projects.id', 'p.id')
                            ->select('id')
                            ->limit(1),
                        'can-assign' =>  Project::from('projects', 'p')
                            ->canBeAssignedBy($request->user())
                            ->whereColumn('projects.id', 'p.id')
                            ->select('id')
                            ->limit(1),
                    ])
                    ->withCasts([
                        'can-view' => 'boolean',
                        'can-edit' => 'boolean',
                        'can-delete' => 'boolean',
                        'can-assign' => 'boolean',
                    ])
                    ->paginate()
            ),

            'priorities' => fn () => ProjectPriorityResource::collection(ProjectPriority::all(['id', 'name'])),

            'supervisors' => fn () => UserResource::collection(
                User::query()
                    ->with(['profile' => fn ($q) => $q->with('media')->select('id', 'name')])
                    ->canSuperviseAProject()
                    ->select('id')
                    ->withForeignKeys()
                    ->get()
            ),

            'canCreateProjects' => fn () => User::canCreateProjects()->whereKey($request->user()->id)->exists(),
        ]);
    }
}
