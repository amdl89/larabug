<?php

namespace App\Http\Controllers;

use App\Enums\ToastType;
use App\Http\Resources\TicketPriorityResource;
use App\Models\TicketPriority;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TicketPriorityController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'searchQuery' => ['sometimes', 'min:4'],
        ]);

        return inertia('TicketPriorities/Index', [
            'priorities' => fn () => TicketPriorityResource::collection(
                TicketPriority::query()
                    ->withCount(['tickets'])
                    ->when(
                        $request->get('searchQuery'),
                        fn ($query, $searchQuery) => $query->searchAndOrder($searchQuery)
                    )
                    ->orderBy('id', 'desc')
                    ->paginate()
            )
        ]);
    }

    public function store(Request $request)
    {
        TicketPriority::create(
            $request->validate([
                'name' => ['required', 'unique:ticket_priorities,name'],
                'color' => ['required']
            ])
        );

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Ticket Priority Added Successfully!'
        ]);

        return back();
    }

    public function update(Request $request, TicketPriority $ticketPriority)
    {
        $ticketPriority->update(
            $request->validate([
                'name' => ['required', Rule::unique('ticket_priorities', 'name')->ignore($ticketPriority->id)],
                'color' => ['required']
            ])
        );

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Ticket Priority Updated Successfully!'
        ]);

        return back();
    }

    public function destroy(Request $request, TicketPriority $ticketPriority)
    {
        $ticketPriority->delete();

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Ticket Priority Deleted Successfully!'
        ]);

        return back();
    }
}
