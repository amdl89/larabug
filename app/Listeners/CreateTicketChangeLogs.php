<?php

namespace App\Listeners;

use App\Enums\TicketProperty;
use App\Events\TicketUpdated;
use App\Models\ChangeLog;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class CreateTicketChangeLogs
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(TicketUpdated $event)
    {
        DB::transaction(function () use ($event)
        {
            $event->newTicket->changeLogs()->createMany(
                $this->changeLogsData(
                    $event->oldTicket,
                    $event->newTicket,
                )
                    ->map(fn ($data) => [
                        'data' => $data,
                        'date' => $event->newTicket->updated_at,
                        'initiatorId' => $event->initiator->id,
                    ])
            );
        });
    }

    private function changeLogsData(Ticket $oldTicket, Ticket $newTicket)
    {
        return collect([])
            ->when(
                $newTicket->title != $oldTicket->title,
                function ($changeLogsData) use ($oldTicket, $newTicket)
                {
                    return $changeLogsData->push([
                        'property' => TicketProperty::Title,
                        'oldValue' => $oldTicket->title,
                        'newValue' => $newTicket->title,
                    ]);
                }
            )
            ->when(
                md5($newTicket->description) != md5($oldTicket->description),
                function ($changeLogsData) use ($oldTicket, $newTicket)
                {
                    return $changeLogsData->push([
                        'property' =>  TicketProperty::Description,
                        'oldValue' => $oldTicket->description,
                        'newValue' => $newTicket->description,
                    ]);
                }
            )
            ->when(
                $newTicket->status->value != $oldTicket->status->value,
                function ($changeLogsData) use ($oldTicket, $newTicket)
                {
                    return $changeLogsData->push([
                        'property' =>  TicketProperty::Status,
                        'oldValue' => $oldTicket->status->key,
                        'newValue' => $newTicket->status->key,
                    ]);
                }
            )
            ->when(
                $newTicket->priorityId != $oldTicket->priorityId,
                function ($changeLogsData) use ($oldTicket, $newTicket)
                {
                    return $changeLogsData->push([
                        'property' =>  TicketProperty::Priority,
                        'oldValue' => $oldTicket->priorityId,
                        'newValue' => $newTicket->priorityId,
                    ]);
                }
            )
            ->when(
                $newTicket->typeId != $oldTicket->typeId,
                function ($changeLogsData) use ($oldTicket, $newTicket)
                {
                    return $changeLogsData->push([
                        'property' =>  TicketProperty::Type,
                        'oldValue' => $oldTicket->typeId,
                        'newValue' => $newTicket->typeId,
                    ]);
                }
            )
            ->when(
                $newTicket->assigneeId != $oldTicket->assigneeId,
                function ($changeLogsData) use ($oldTicket, $newTicket)
                {
                    return $changeLogsData->push([
                        'property' =>  TicketProperty::Assignee,
                        'oldValue' => $oldTicket->assigneeId,
                        'newValue' => $newTicket->assigneeId,
                    ]);
                }
            );
    }
}
