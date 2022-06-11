<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Http\Requests\FilterTicketRequest;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\TicketPriorityResource;
use App\Http\Resources\TicketResource;
use App\Http\Resources\TicketTypeResource;
use App\Http\Resources\UserResource;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserAssignedTicketController extends Controller
{
    public function index(FilterTicketRequest $request, User $user)
    {
        Gate::authorize('view-all-assigned-ticket', [$user]);

        return inertia('Users/AssignedTickets/Index', [
            'totalTicketCount' => fn () => $user->assignedTickets()->count(),

            'openOrReopendedTicketCount' => fn () => $user->assignedTickets()
                ->whereIn('status', [TicketStatus::Open, TicketStatus::Reopended])
                ->count(),

            'unassignedTicketCount' => fn () => $user->assignedTickets()
                ->whereDoesntHave('assignee')
                ->count(),

            'closedTicketCount' => fn () => $user->assignedTickets()
                ->where('status', TicketStatus::Closed)
                ->count(),

            'tickets' => fn () => TicketResource::collection(
                $user->assignedTickets()
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
                        collect($request->get('filters'))->except('devs')->all(),
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

            'devs' => fn () => UserResource::collection([
                $request->user()->load(['profile' => fn ($q) => $q->select('id', 'name')])
            ]),

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

            'canAssignTicket' => fn () => User::canAssignTicketsForOwnProject()->whereKey($user->id)->exists(),
        ]);
    }
}
