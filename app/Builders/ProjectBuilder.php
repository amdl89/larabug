<?php

namespace App\Builders;

use App\Enums\DateRange;
use App\Enums\ProjectStatus;
use App\Models\User;
use DB;
use Illuminate\Database\Eloquent\Builder;

class ProjectBuilder extends Builder
{
    public function withSupervisor()
    {
        $this->addSelect(['supervisorId' => $this->supervisorIdQuery()])
            ->with('supervisor');

        return $this;
    }

    private function supervisorIdQuery()
    {
        return User::query()
            ->select('id')
            ->canSuperviseAProject()
            ->whereIn('id', function ($q1)
            {
                $q1->select('userId')
                    ->from('project_users')
                    ->whereColumn('projectId', 'projects.id');
            })
            ->take(1);
    }

    public function whereOverDue()
    {
        $this->whereDate('deadline', '<=', now());

        return $this;
    }

    public function filter(array $filters)
    {
        $this->when(
            $filters['status'] ?? null,
            fn ($query, $status) => $query->whereStatusKey($status)
        )
            ->when(
                $filters['priorities'] ?? null,
                fn ($query, $priorities) => $query->whereHas(
                    'priority',
                    fn ($q) => $q->whereIn('id', $priorities)
                )
            )->when(
                $filters['createdRange'] ?? null,
                fn ($query, $createdRange) => $query->whereCreatedRange($createdRange)
            )
            ->when(
                $filters['supervisors'] ?? null,
                fn ($query, $supervisors) => $query->whereHasSupervisor(
                    fn ($q) => $q->whereIn('users.id', $supervisors)
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
            case 'deadline':
                $this->orderBy($sortBy['field'], $sortBy['order']);
                break;

            case 'createdAt':
                $this->orderBy('created_at', $sortBy['order']);
                break;

            case 'priority.name':
                $this->orderByPriorityName($sortBy['order']);
                break;

            case 'supervisor.profile.name':
                $this->orderBySupervisorName($sortBy['order']);
                break;
        };

        return $this;
    }

    public function whereStatusKey(string $statusKey)
    {
        $this->where(
            'status',
            ProjectStatus::fromKey($statusKey)->value
        );

        return $this;
    }

    public function whereCreatedRange(string $range)
    {
        switch ($range)
        {
            case DateRange::All:
                break;

            case DateRange::ThisWeek:
                $this->whereDate('created_at', '>=', now()->subWeek());
                break;

            case DateRange::ThisMonth:
                $this->whereDate('created_at', '>=', now()->subMonth());
                break;

            case DateRange::ThisYear:
                $this->whereDate('created_at', '>=', now()->subYear());
                break;
        }

        return $this;
    }

    public function whereHasSupervisor($additionalWhereQuery)
    {
        $this->addWhereExistsQuery(
            $this->supervisorIdQuery()
                ->where($additionalWhereQuery)
                ->toBase()
        );

        return $this;
    }

    public function orderByPriorityName($order = 'asc')
    {
        $this->orderBy(
            function ($query)
            {
                $query->select('name')
                    ->from('project_priorities')
                    ->whereColumn(
                        'project_priorities.id',
                        'projects.priorityId'
                    )
                    ->limit(1);
            },
            $order
        );

        return $this;
    }

    public function orderBySupervisorName($order = 'asc')
    {
        $this->orderBy(
            function ($query)
            {
                $query->select('name')
                    ->from('profiles')
                    ->whereColumn(
                        'profiles.userId',
                        'supervisorId'
                    )
                    ->limit(1);
            },
            $order
        );

        return $this;
    }
}
