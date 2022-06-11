<?php

namespace App\Builders;

use App\Enums\DateRange;
use App\Enums\ReceivedMessageStatus;
use Illuminate\Database\Eloquent\Builder;

class MessageBuilder extends Builder
{
    public function latestFirst($column = 'created_at')
    {
        $this->orderBy("messages.$column", 'desc')
            ->orderBy('messages.id', 'desc');

        return $this;
    }

    public function filter(array $filters)
    {
        $this->when(
            $filters['senders'] ?? null,
            fn ($query, $senders) => $query->whereHas(
                'sender',
                fn ($q) => $q->whereIn('id', $senders)
            )
        )
            ->when(
                $filters['receipents'] ?? null,
                fn ($query, $receipents) => $query->whereHas(
                    'receipents',
                    fn ($q) => $q->whereIn('users.id', $receipents)
                )
            )
            ->when(
                $filters['createdRange'] ?? null,
                fn ($query, $range) => $query->whereCreatedRange($range)
            )
            ->when(
                $filters['receivedInfo.receivedStatus'] ?? null,
                fn ($query, $status) => $query->wherePivotReceivedStatus($status)
            )
            ->when(
                $filters['receivedInfo.createdRange'] ?? null,
                fn ($query, $range) => $query->wherePivotCreatedRange($range)
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

    // pivot scopes

    public function wherePivotReceivedStatus(string $statusKey)
    {
        return $this->where(
            'user_received_messages.receivedStatus',
            ReceivedMessageStatus::fromKey($statusKey)->value
        );
    }

    public function wherePivotCreatedRange(string $range)
    {
        switch ($range)
        {
            case DateRange::All:
                break;

            case DateRange::ThisWeek:
                $this->whereDate('user_received_messages.created_at', '>=', now()->subWeek());
                break;

            case DateRange::ThisMonth:
                $this->whereDate('user_received_messages.created_at', '>=', now()->subMonth());
                break;

            case DateRange::ThisYear:
                $this->whereDate('user_received_messages.created_at', '>=', now()->subYear());
                break;
        }

        return $this;
    }

    public function orderByLatestPivot($column = 'created_at')
    {
        $this->orderBy("user_received_messages.$column", 'desc')
            ->orderBy('user_received_messages.id', 'desc');

        return $this;
    }
}
