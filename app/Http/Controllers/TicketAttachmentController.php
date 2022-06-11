<?php

namespace App\Http\Controllers;

use App\Enums\ToastType;
use App\Http\Requests\StoreAttachmentRequest;
use App\Models\Ticket;
use DB;
use Illuminate\Support\Facades\Gate;

class TicketAttachmentController extends Controller
{
    public function store(StoreAttachmentRequest $request, Ticket $ticket)
    {
        Gate::authorize('store-ticket-attachment', [$ticket]);

        DB::transaction(function () use ($request, $ticket)
        {
            $attachment = $ticket->attachments()
                ->make([
                    'name' => $request->get('name'),
                    'notes' => $request->get('notes'),
                ]);

            $attachment->uploader()->associate($request->user());
            $attachment->save();

            $attachment->addMediaFromRequest('attachedFile')
                ->toMediaCollection('attachedFile');
        });

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Attachment Added Successfully!'
        ]);

        return back();
    }
}
