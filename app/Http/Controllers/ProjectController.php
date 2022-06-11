<?php

namespace App\Http\Controllers;

use App\Enums\ProjectStatus;
use App\Enums\TicketStatus;
use App\Enums\ToastType;
use App\Http\Requests\FilterProjectsRequest;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\AttachmentResource;
use App\Http\Resources\ProjectPriorityResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\UserResource;
use App\Models\Attachment;
use App\Models\Project;
use App\Models\ProjectPriority;
use App\Models\TicketPriority;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    public function index(FilterProjectsRequest $request)
    {
        Gate::authorize('view-all-project');

        return inertia('Projects/Index', [
            'totalProjectCount' => fn () => Project::count(),

            'activeProjectCount' => fn () => Project::query()
                ->where('status', ProjectStatus::Active)
                ->count(),

            'overdueProjectCount' => fn () => Project::query()
                ->whereOverDue()
                ->count(),

            'projects' => fn () => ProjectResource::collection(
                Project::query()
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

    public function show(Request $request, Project $project)
    {
        Gate::authorize('show-project', [$project]);

        return inertia('Projects/Show', [
            'project' => fn () => new ProjectResource(
                $project->query()
                    ->withSupervisor()
                    ->with([
                        'media',
                        'priority' => fn ($q) => $q->select('id', 'name', 'color'),
                        'supervisor' => fn ($q1) => $q1->with(
                            ['profile' => fn ($q2) => $q2->with('media')->select('id', 'name')]
                        )
                            ->select('users.id')
                            ->withForeignKeys(),
                        'devs' => fn ($q1) => $q1->with(
                            ['profile' => fn ($q2) => $q2->with('media')->select('id', 'name')]
                        )
                            ->select('users.id')
                            ->withForeignKeys(),
                        'testers' => fn ($q1) => $q1->with(
                            ['profile' => fn ($q2) => $q2->with('media')->select('id', 'name')]
                        )
                            ->select('users.id')
                            ->withForeignKeys(),
                    ])
                    ->find($project->id)
            ),

            'supervisors' => fn () => UserResource::collection(
                User::query()
                    ->with(['profile' => fn ($q) => $q->with('media')->select('id', 'name')])
                    ->canSuperviseAProject()
                    ->select('id')
                    ->withForeignKeys()
                    ->get()
            ),

            'priorities' => fn () => ProjectPriorityResource::collection(ProjectPriority::all(['id', 'name'])),

            'attachments' => fn () => AttachmentResource::collection(
                $project->attachments()
                    ->with([
                        'media',
                        'uploader' => fn ($q1) => $q1->with(
                            ['profile' => fn ($q2) => $q2->select('id', 'name')]
                        )
                            ->select('id')
                            ->withForeignKeys(),
                    ])
                    ->orderBy('id', 'desc')
                    ->addSelect([
                        'can-view' => Attachment::from('attachments', 'a')
                            ->canBeViewedBy($request->user())
                            ->whereColumn('attachments.id', 'a.id')
                            ->select('id')
                            ->limit(1),
                        'can-delete' =>  Attachment::from('attachments', 'a')
                            ->canBeDeletedBy($request->user())
                            ->whereColumn('attachments.id', 'a.id')
                            ->select('id')
                            ->limit(1),
                    ])
                    ->withCasts([
                        'can-view' => 'boolean',
                        'can-delete' => 'boolean',
                    ])
                    ->paginate(null, ['*'], 'attachmentPage')
            ),

            'ticketPriorityChartData' => TicketPriority::query()
                ->withCount([
                    'tickets' => fn ($query) => $query->whereHas(
                        'project',
                        fn ($q) => $q->where('id', $project->id)
                    )
                ])
                ->get()
                ->pipe(
                    fn ($ticketPriorityWithTicketCount) => [
                        'chartLabels' => $ticketPriorityWithTicketCount->pluck('name'),
                        'labelToColorMap' => $ticketPriorityWithTicketCount->pluck('color', 'name'),
                        'labelToDataMap' => $ticketPriorityWithTicketCount->pluck('tickets_count', 'name'),
                    ]
                ),

            'ticketStatusChartData' => [
                'chartLabels' => collect(TicketStatus::asArray())->keys(),
                'labelToDataMap' => $project->tickets()
                    ->selectRaw('status, count(id) as tickets_count')
                    ->groupBy('status')
                    ->get()
                    ->pluck('tickets_count', 'status')
                    ->mapWithKeys(
                        fn ($ticketsCount, $statusValue) => [
                            TicketStatus::fromValue($statusValue)->key => $ticketsCount
                        ]
                    )
                    ->pipe(
                        fn ($ticketsCounts) => collect(
                            TicketStatus::asArray()
                        )
                            ->map(fn ($ts) => 0)
                            ->merge($ticketsCounts)
                    ),
            ],

            'ticketTypeChartData' => TicketType::query()
                ->withCount([
                    'tickets' => fn ($query) => $query->whereHas(
                        'project',
                        fn ($q) => $q->where('id', $project->id)
                    )
                ])
                ->get()
                ->pipe(fn ($ticketTypes) => [
                    'chartLabels' => $ticketTypes->pluck('name'),
                    'labelToColorMap' => $ticketTypes->pluck('color', 'name'),
                    'labelToDataMap' => $ticketTypes->pluck('tickets_count', 'name'),
                ]),

            'can' => fn () => [
                'update-project-supervisor' => Gate::allows('update-project-supervisor'),

                'update-project' => Gate::allows('update-project', [$project]),

                'destroy-project' => Gate::allows('destroy-project'),

                'store-project-attachment' => Gate::allows('store-project-attachment', [$project]),

                'view-all-project-ticket' => Gate::allows('view-all-project-ticket', [$project]),

                'update-project-cover-image' => Gate::allows('update-project-cover-image', [$project]),

                'destroy-project-cover-image' => Gate::allows('destroy-project-cover-image', [$project]),

                'edit-all-project-team' => Gate::allows('edit-all-project-team'),
            ],
        ]);
    }

    public function store(StoreProjectRequest $request)
    {
        Gate::authorize('store-project');

        DB::transaction(function () use ($request)
        {
            $project = Project::make([
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'status' => ProjectStatus::Active,
                'deadline' => $request->get('deadline'),
            ]);

            $project->priority()->associate($request->get('priority'));

            if ($request->file('coverImage'))
            {
                $project->addMediaFromRequest('coverImage')
                    ->toMediaCollection('coverImage');
            }

            $project->save();
            $project->associateSupervisor($request->get('supervisor'));
        });

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Project Created Successfully!'
        ]);

        return back();
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        Gate::authorize('update-project', [$project]);

        if ($request->has('supervisor'))
            Gate::authorize('update-project-supervisor');

        DB::transaction(function () use ($request, $project)
        {
            $project->fill([
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'status' => ProjectStatus::fromKey($request->get('status'))->value,
                'deadline' => $request->get('deadline'),
            ]);

            $project->priority()->associate($request->get('priority'));
            $project->save();

            if ($request->has('supervisor'))
                $project->associateSupervisor($request->get('supervisor'));
        });

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Project Updated Successfully!'
        ]);

        return back();
    }

    public function destroy(Request $request, Project $project)
    {
        Gate::authorize('destroy-project', [$project]);

        $project->deletePreservingMedia();

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Project Deleted Successfully!'
        ]);

        if ($request->get('redirectUrl'))
        {
            return redirect($request->get('redirectUrl'));
        }
        return back();
    }
}
