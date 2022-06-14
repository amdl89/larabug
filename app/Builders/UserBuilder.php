<?php

namespace App\Builders;

use App\Enums\DateRange;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Builder;

class UserBuilder extends Builder
{
    public function whereRole(string $role)
    {
        $this->whereHas('roles', fn ($query) => $query->where('name', $role));

        return $this;
    }

    public function whereRoles(array $roles)
    {
        $this->whereHas('roles', fn ($query) => $query->whereIn('name', $roles));

        return $this;
    }

    public function whereAddedThisMonth()
    {
        $this->whereDate('created_at', '>=', now()->subMonth());

        return $this;
    }

    public function filter(array $filters)
    {
        $this->when(
            $filters['roles'] ?? null,
            fn ($query, $roles) => $query->whereHas('roles', fn ($q) => $q->whereIn('id', $roles))
        )->when(
            $filters['createdRange'] ?? null,
            fn ($query, $createdRange) => $query->whereCreatedRange($createdRange)
        )->when(
            $filters['projects'] ?? null,
            fn ($query, $projects) => $query->whereHas(
                'projects',
                fn ($q) => $q->whereIn('projects.id', $projects)
            )
        );

        return $this;
    }

    public function sort(array $sortBy)
    {
        switch ($sortBy['field'])
        {
            case 'createdAt':
                $this->orderBy('created_at', $sortBy['order']);
                break;

            case 'email':
                $this->orderBy('email', $sortBy['order']);
                break;

            case 'profile.name':
                $this->orderByName($sortBy['order']);
                break;
        };

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

    public function orderByName($order = 'asc')
    {
        $this->orderBy(
            Profile::select('name')
                ->whereColumn('users.profileId', 'profiles.id')
                ->limit(1),
            $order
        );

        return $this;
    }
}
