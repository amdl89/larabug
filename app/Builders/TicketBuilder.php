<?php

namespace App\Builders;

use App\Enums\DateRange;
use App\Enums\TicketStatus;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Builder;

class TicketBuilder extends Builder
{
    public function filter(array $filters)
    {
        $this->when(
            $filters['devs'] ?? null,
            fn ($query, $devs) => $query->whereHas(
                'assignee',
                fn ($q) => $q->whereIn('id', $devs)
            )
        )->when(
            $filters['submitters'] ?? null,
            fn ($query, $submitters) => $query->whereHas(
                'creator',
                fn ($q) => $q->whereIn('id', $submitters)
            )
        )->when(
            $filters['priorities'] ?? null,
            fn ($query, $priorities) => $query->whereHas(
                'priority',
                fn ($q) => $q->whereIn('id', $priorities)
            )
        )->when(
            $filters['types'] ?? null,
            fn ($query, $types) => $query->whereHas(
                'type',
                fn ($q) => $q->whereIn('id', $types)
            )
        )->when(
            $filters['statuses'] ?? null,
            fn ($query, $statuses) => $query->whereStatusKeyIn($statuses)
        )->when(
            $filters['updatedRange'] ?? null,
            fn ($query, $updatedRange) => $query->whereUpdatedRange($updatedRange)
        )->when(
            $filters['projects'] ?? null,
            fn ($query, $projects) => $query->whereHas(
                'project',
                fn ($q) => $q->whereIn('id', $projects)
            )
        );

        return $this;
    }

    public function sort(array $sortBy)
    {
        switch ($sortBy['field'])
        {
            case 'title':
            case 'status':
                $this->orderBy($sortBy['field'], $sortBy['order']);
                break;

            case 'updatedAt':
                $this->orderBy('updated_at', $sortBy['order']);
                break;

            case 'creator.profile.name':
                $this->orderByCreatorName($sortBy['order']);
                break;

            case 'assignee.profile.name':
                $this->orderByAssigneeName($sortBy['order']);
                break;

            case 'priority.name':
                $this->orderByPriorityName($sortBy['order']);
                break;
        };

        return $this;
    }

    public function whereStatusKeyIn(array $statusKeys)
    {
        $this->whereIn(
            'status',
            collect($statusKeys)->map(fn ($statusKey) => TicketStatus::fromKey($statusKey)->value)
        );

        return $this;
    }

    public function whereUpdatedRange(string $range)
    {
        switch ($range)
        {
            case DateRange::All:
                break;

            case DateRange::ThisWeek:
                $this->whereDate('updated_at', '>=', now()->subWeek());
                break;

            case DateRange::ThisMonth:
                $this->whereDate('updated_at', '>=', now()->subMonth());
                break;

            case DateRange::ThisYear:
                $this->whereDate('updated_at', '>=', now()->subYear());
                break;
        }

        return $this;
    }

    public function orderByCreatorName($order = 'asc')
    {
        $this->orderBy(
            Profile::select('name')
                ->whereHas('user', fn ($q) => $q->whereColumn('users.id', 'tickets.creatorId'))
                ->limit(1),
            $order
        );

        return $this;
    }

    public function orderByAssigneeName($order = 'asc')
    {
        $this->orderBy(
            Profile::select('name')
                ->whereHas('user', fn ($q) => $q->whereColumn('users.id', 'tickets.assigneeId'))
                ->limit(1),
            $order
        );

        return $this;
    }

    public function orderByPriorityName($order = 'asc')
    {
        $this->orderBy(
            function ($query)
            {
                $query->select('name')
                    ->from('ticket_priorities')
                    ->whereColumn(
                        'ticket_priorities.id',
                        'tickets.priorityId'
                    )
                    ->limit(1);
            },
            $order
        );

        return $this;
    }

    public function orderByTypeName($order = 'asc')
    {
        $this->orderBy(
            function ($query)
            {
                $query->select('name')
                    ->from('ticket_types')
                    ->whereColumn(
                        'ticket_types.id',
                        'tickets.typeId'
                    )
                    ->limit(1);
            },
            $order
        );

        return $this;
    }
}
