<?php

namespace App\Models;

class TicketStatusWorkflow
{
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }
}
