<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Enums\ToastType;
use App\Enums\UserRole;
use App\Http\Requests\FilterTicketRequest;
use App\Http\Requests\ShowTicketRequest;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Http\Resources\AttachmentResource;
use App\Http\Resources\ChangeLogResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\TicketPriorityResource;
use App\Http\Resources\TicketResource;
use App\Http\Resources\TicketTypeResource;
use App\Http\Resources\UserResource;
use App\Models\Attachment;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TicketController extends Controller
{
    public function index(FilterTicketRequest $request)
    {
        Gate::authorize('view-all-ticket');

        return inertia('Tickets/Index', [
            'totalTicketCount' => fn () => Ticket::count(),

            'openOrReopendedTicketCount' => fn () => Ticket::query()
                ->whereIn('status', [TicketStatus::Open, TicketStatus::Reopended])
                ->count(),

            'unassignedTicketCount' => fn () => Ticket::query()
                ->whereDoesntHave('assignee')
                ->count(),

            'closedTicketCount' => fn () => Ticket::query()
                ->where('status', TicketStatus::Closed)
                ->count(),

            'tickets' => fn () => TicketResource::collection(
                Ticket::query()
                    ->with([
                        'priority' => fn ($q) => $q->select('id', 'name', 'color'),
                        'type' => fn ($q) => $q->select('id', 'name', 'color'),
                        'creator' => fn ($q1) => $q1->with(
                            ['profile' => fn ($q2) => $q2->select('id', 'name')]
                        )
                            ->select('id')
                            ->withForeignKeys(),
                        'assignee' => fn ($q1) => $q1->with(
                            ['profile' => fn ($q2) => $q2->select('id', 'name')]
                        )
                            ->select('id')
                            ->withForeignKeys(),
                        'project' => fn ($q1) => $q1->with(
                            ['devs' => fn ($q2) => $q2->with(
                                ['profile' => fn ($q3) => $q3->with('media')->select('id', 'name')]
                            )
                                ->select('users.id')
                                ->withForeignKeys()]
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
                        'can-view' => Ticket::from('tickets', 't')
                            ->canBeViewedBy($request->user())
                            ->whereColumn('tickets.id', 't.id')
                            ->select('id')
                            ->limit(1),
                        'can-edit' =>  Ticket::from('tickets', 't')
                            ->canBeModifiedBy($request->user())
                            ->whereColumn('tickets.id', 't.id')
                            ->select('id')
                            ->limit(1),
                        'can-delete' =>  Ticket::from('tickets', 't')
                            ->canBeDeletedBy($request->user())
                            ->whereColumn('tickets.id', 't.id')
                            ->select('id')
                            ->limit(1),
                        'can-assign' =>  Ticket::from('tickets', 't')
                            ->canBeAssignedBy($request->user())
                            ->whereColumn('tickets.id', 't.id')
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

            'devs' => fn () => UserResource::collection(
                User::query()
                    ->with(['profile' => fn ($q) => $q->select('id', 'name')])
                    ->whereHas(
                        'roles',
                        fn ($q) => $q->where('name', UserRole::Dev)
                    )
                    ->select('id')
                    ->withForeignKeys()
                    ->get()
            ),

            'submitters' => fn () => UserResource::collection(
                User::query()
                    ->with(['profile' => fn ($q) => $q->select('id', 'name')])
                    ->select('id')
                    ->withForeignKeys()
                    ->get()
            ),

            'priorities' => fn () => TicketPriorityResource::collection(TicketPriority::all(['id', 'name'])),

            'types' => fn () => TicketTypeResource::collection(TicketType::all(['id', 'name'])),

            'projects' => fn () => ProjectResource::collection(
                Project::query()
                    ->canHaveTicketAddedBy($request->user())
                    ->select('id', 'title')
                    ->get()
            ),

            'projectsForFilter' => fn () => ProjectResource::collection(Project::all(['id', 'title'])),

            'canAssignTicket' => fn () => User::canAssignTicketsForOwnProject()->whereKey($request->user()->id)->exists(),

        ]);
    }

    public function show(ShowTicketRequest $request, Ticket $ticket)
    {
        Gate::authorize('show-ticket', [$ticket]);

        return inertia('Tickets/Show', [
            'ticket' => fn () => new TicketResource(
                $ticket->load([
                    'priority' => fn ($q) => $q->select('id', 'name', 'color'),
                    'type' => fn ($q) => $q->select('id', 'name', 'color'),
                    'creator' => fn ($q1) => $q1->with(
                        ['profile' => fn ($q2) => $q2->select('id', 'name')]
                    )
                        ->select('id')
                        ->withForeignKeys(),
                    'assignee' => fn ($q1) => $q1->with(
                        ['profile' => fn ($q2) => $q2->select('id', 'name')]
                    )
                        ->select('id')
                        ->withForeignKeys(),
                    'project' => fn ($q1) => $q1->with(
                        ['devs' => fn ($q2) => $q2->with(
                            ['profile' => fn ($q3) => $q3->with('media')->select('id', 'name')]
                        )
                            ->select('users.id')
                            ->withForeignKeys()]
                    )
                        ->select('id', 'title')
                        ->withForeignKeys(),
                ])
            ),

            'attachments' => fn () => AttachmentResource::collection(
                $ticket->attachments()
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

            'types' => fn () => TicketTypeResource::collection(TicketType::all(['id', 'name'])),

            'priorities' => fn () => TicketPriorityResource::collection(TicketPriority::all(['id', 'name'])),

            'changeLogs' => fn () => ChangeLogResource::collection(
                $ticket->changeLogs()
                    ->with([
                        'initiator' => fn ($q1) => $q1->with(
                            ['profile' => fn ($q2) => $q2->select('id', 'name')]
                        )
                            ->select('id')
                            ->withForeignKeys()
                    ])
                    ->when(
                        $request->get('ticketChangeLogFilters'),
                        fn ($query, $ticketChangeLogFilters) => $query->filter($ticketChangeLogFilters)
                    )
                    ->when(
                        $request->get('ticketChangeLogSortOrder'),
                        fn ($query, $ticketChangeLogSortOrder) => $query->orderBy('id', $ticketChangeLogSortOrder),
                        fn ($query) => $query->orderBy('id', 'desc')
                    )
                    ->select('id', 'date')
                    ->withResolvedDataForTicket()
                    ->withForeignKeys()
                    ->paginate(null, ['*'], 'ticketChangeLogPage'),
            ),

            'ticketModifiers' => fn () => UserResource::collection(
                User::query()
                    ->canModifyTicket($ticket)
                    ->orWhere(
                        function ($query) use ($ticket)
                        {
                            $query->whereRole(UserRole::Dev)
                                ->whereHas('projects.tickets', fn ($q) => $q->whereKey($ticket->id));
                        }
                    )
                    ->with(['profile' => fn ($q) => $q->select('id', 'name')])
                    ->get()
            ),

            'can' => fn () => [
                'store-ticket-assignee' => Gate::allows('store-ticket-assignee', [$ticket]),

                'update-ticket' => Gate::allows('update-ticket', [$ticket]),

                'destroy-ticket' => Gate::allows('destroy-ticket', [$ticket]),

                'store-ticket-attachment' => Gate::allows('store-ticket-attachment', [$ticket]),

                'store-comment' => Gate::allows('store-comment', [$ticket]),
            ],
        ]);
    }

    public function store(StoreTicketRequest $request)
    {
        $project = Project::findOrFail($request->get('project'));

        Gate::authorize('store-ticket', [$project]);

        if ($request->has('assignee'))
            Gate::authorize('store-ticket-assignee-for-project', [$project]);

        $ticket = Ticket::make([
            'title' => $request->get('title'),
            'description' => $request->get('description'),
            'status' => TicketStatus::Open,
        ]);

        $ticket->priority()->associate($request->get('priority'));
        $ticket->type()->associate($request->get('type'));
        $ticket->creator()->associate($request->user());
        $ticket->project()->associate($request->get('project'));

        if ($request->has('assignee'))
            $ticket->assignee()->associate($request->get('assignee'));

        $ticket->save();

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Ticket Added Successfully!'
        ]);

        return back();
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        Gate::authorize('update-ticket', [$ticket]);

        if ($request->has('assignee'))
            Gate::authorize('store-ticket-assignee', [$ticket]);

        $ticket->fill([
            'title' => $request->get('title'),
            'description' => $request->get('description'),
            'status' => TicketStatus::fromKey($request->get('status'))->value,
        ]);

        $ticket->priority()->associate($request->get('priority'));
        $ticket->type()->associate($request->get('type'));

        if ($request->has('assignee'))
            $ticket->assignee()->associate($request->get('assignee'));

        $ticket->save();

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Ticket Updated Successfully!'
        ]);

        return back();
    }

    public function destroy(Request $request, Ticket $ticket)
    {
        Gate::authorize('destroy-ticket', [$ticket]);

        $ticket->delete();

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Ticket Deleted Successfully!'
        ]);

        if ($request->get('redirectUrl'))
        {
            return redirect($request->get('redirectUrl'));
        }
        return back();
    }
}
