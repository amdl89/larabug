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
                        WHEN JSON_EXTRACT(`data`, '$.property') = 'Priority' THEN JSON_OBJECT('property', JSON_EXTRACT(`data`, '$.property'), 'oldValue', (SELECT `name` FROM ticket_priorities WHERE id = JSON_EXTRACT(`data`, '$.oldValue')), 'newValue', (SELECT `name` FROM ticket_priorities WHERE id = JSON_EXTRACT(`data`, '$.newValue')))
                        WHEN JSON_EXTRACT(`data`, '$.property') = 'Type' THEN JSON_OBJECT('property', JSON_EXTRACT(`data`, '$.property'), 'oldValue', (SELECT `name` FROM ticket_types WHERE id = JSON_EXTRACT(`data`, '$.oldValue')), 'newValue', (SELECT `name` FROM ticket_types WHERE id = JSON_EXTRACT(`data`, '$.newValue')))
                        WHEN JSON_EXTRACT(`data`, '$.property') = 'Assignee' THEN JSON_OBJECT('property', JSON_EXTRACT(`data`, '$.property'), 'oldValue', (SELECT `name` FROM profiles WHERE id = (SELECT profileId FROM users WHERE id = JSON_EXTRACT(`data`, '$.oldValue'))), 'newValue', (SELECT `name` FROM profiles WHERE id = (select profileId from users where id = JSON_EXTRACT(`data`, '$.newValue'))))
                        ELSE `data`
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
