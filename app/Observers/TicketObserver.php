<?php

namespace App\Observers;

use App\Events\TicketUpdated;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketObserver
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function updated(Ticket $ticket)
    {
        TicketUpdated::dispatch(
            Ticket::make($ticket->getOriginal())->setAttribute('id', $ticket->id),
            $ticket,
            $this->request->user()
        );
    }
}
