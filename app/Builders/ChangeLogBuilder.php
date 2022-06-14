<?php

namespace App\Builders;

use App\Enums\DateRange;
use Illuminate\Database\Eloquent\Builder;

class ChangeLogBuilder extends Builder
{
    public function filter(array $filters)
    {
        $this->when(
            $filters['property'] ?? null,
            fn ($query, $property) => $query->where('data->property', $property)
        )->when(
            $filters['initiator'] ?? null,
            fn ($query, $initiator) => $query->where('initiatorId', $initiator)

        )->when(
            $filters['dateRange'] ?? null,
            fn ($query, $dateRange) => $query->whereDateRange($dateRange)
        );

        return $this;
    }

    public function whereDateRange(string $range)
    {
        switch ($range)
        {
            case DateRange::All:
                break;

            case DateRange::ThisWeek:
                $this->whereDate('date', '>=', now()->subWeek());
                break;

            case DateRange::ThisMonth:
                $this->whereDate('date', '>=', now()->subMonth());
                break;

            case DateRange::ThisYear:
                $this->whereDate('date', '>=', now()->subYear());
                break;
        }

        return $this;
    }

    public function withResolvedDataForTicket()
    {
        $this->addSelect([
            'resolvedData' => function ($query)
            {
                $query->selectRaw(
                    <<<'SQL'
                    (CASE
                        WHEN data->>'property' = 'Priority' THEN json_build_object('property', data->>'property', 'oldValue', (SELECT tp.name FROM ticket_priorities as tp WHERE id = (data->>'oldValue')::bigint), 'newValue', (SELECT tp.name FROM ticket_priorities as tp WHERE id = (data->>'newValue')::bigint))
                        WHEN data->>'property' = 'Type' THEN json_build_object('property', data->>'property', 'oldValue', (SELECT tt.name FROM ticket_types as tt WHERE id = (data->>'oldValue')::bigint), 'newValue', (SELECT tt.name FROM ticket_types as tt WHERE id = (data->>'newValue')::bigint))
                        WHEN data->>'property' = 'Assignee' THEN json_build_object('property', data->>'property', 'oldValue', (SELECT pr.name FROM profiles as pr WHERE id = (SELECT "profileId" FROM users WHERE id = (data->>'oldValue')::bigint)), 'newValue', (SELECT pr.name FROM profiles as pr WHERE id = (select "profileId" from users where id = (data->>'newValue')::bigint)))
                        ELSE data
                    END)
                    SQL
                );
            },
        ])->withCasts(
            ['resolvedData' => 'array']
        );

        return $this;
    }
}
