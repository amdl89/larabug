<?php

namespace App\Http\Controllers;

use App\Enums\ToastType;
use App\Models\Ticket;
use App\Rules\DevWithIdExists;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TicketAssigneeController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        Gate::authorize('store-ticket-assignee', [$ticket]);

        $request->validate([
            'assignee' => ['nullable', new DevWithIdExists,],
        ]);

        $ticket->assignee()->associate($request->get('assignee'))->save();

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Dev Assigned Successfully!'
        ]);

        return back();
    }
}
