<?php

namespace App\Http\Controllers;

use App\Enums\ProjectStatus;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Project;
use App\Models\ProjectPriority;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    public function __invoke()
    {
        Gate::authorize('view-dashboard');

        return inertia('Dashboard', [
            'activeProjectsCount' => Project::where('status', ProjectStatus::Active)->count(),

            'unassignedTicketsCount' => Ticket::whereDoesntHave('assignee')->count(),

            'ticketsThisWeekCount' => Ticket::whereDate('created_at', '>=', now()->subWeek())->count(),

            'ticketsClosedThisWeekCount' => Ticket::whereDate('updated_at', '>=', now()->subWeek())
                ->where('status', TicketStatus::Closed)
                ->count(),

            'ticketsCount' => Ticket::count(),

            'usersRegisteredThisMonthCount' => User::whereDate('created_at', '>=', now()->subDays(30))->count(),

            'usersCount' => User::count(),

            'ticketPriorityChartData' => TicketPriority::withCount('tickets')->get()
                ->pipe(
                    fn ($ticketPriorityWithTicketCount) => [
                        'chartLabels' => $ticketPriorityWithTicketCount->pluck('name'),
                        'labelToColorMap' => $ticketPriorityWithTicketCount->pluck('color', 'name'),
                        'labelToDataMap' => $ticketPriorityWithTicketCount->pluck('tickets_count', 'name'),
                    ]
                ),

            'ticketStatusChartData' => [
                'chartLabels' => collect(TicketStatus::asArray())->keys(),
                'labelToDataMap' => Ticket::query()
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

            'ticketTypeChartData' => TicketType::withCount('tickets')->get()
                ->pipe(fn ($ticketTypes) => [
                    'chartLabels' => $ticketTypes->pluck('name'),
                    'labelToColorMap' => $ticketTypes->pluck('color', 'name'),
                    'labelToDataMap' => $ticketTypes->pluck('tickets_count', 'name'),
                ]),

            'projectPriorityChartData' => ProjectPriority::withCount('projects')->get()
                ->pipe(fn ($projectPriorities) => [
                    'chartLabels' => $projectPriorities->pluck('name'),
                    'labelToColorMap' => $projectPriorities->pluck('color', 'name'),
                    'labelToDataMap' => $projectPriorities->pluck('projects_count', 'name'),
                ]),

            'projectUserChartData' => Project::query()
                ->withCount(['devs', 'testers'])
                ->get()
                ->pipe(function ($projectWihtUsers)
                {
                    return [
                        'chartLabels' => $projectWihtUsers->pluck('title'),
                        'labelToTestersCountMap' => $projectWihtUsers->pluck('testers_count', 'title'),
                        'labelToDevelopersCountMap' => $projectWihtUsers->pluck('devs_count', 'title'),
                    ];
                }),

            'projectTicketChartData' => Project::query()
                ->withCount('tickets')
                ->get()
                ->pipe(function ($projectWithTickets)
                {
                    return [
                        'chartLabels' => $projectWithTickets->pluck('title'),
                        'labelToDataMap' => $projectWithTickets->pluck('tickets_count', 'title'),
                    ];
                }),
        ]);
    }
}
