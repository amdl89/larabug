<?php

namespace App\Http\Controllers;

use App\Enums\ToastType;
use App\Http\Resources\TicketTypeResource;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TicketTypeController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'searchQuery' => ['sometimes', 'min:4'],
        ]);

        return inertia('TicketTypes/Index', [
            'types' => fn () => TicketTypeResource::collection(
                TicketType::query()
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
        TicketType::create(
            $request->validate([
                'name' => ['required', 'unique:ticket_types,name'],
                'color' => ['required']
            ])
        );

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Ticket Type Added Successfully!'
        ]);

        return back();
    }

    public function update(Request $request, TicketType $ticketType)
    {
        $ticketType->update(
            $request->validate([
                'name' => ['required', Rule::unique('ticket_types', 'name')->ignore($ticketType->id)],
                'color' => ['required']
            ])
        );

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Ticket Type Updated Successfully!'
        ]);

        return back();
    }

    public function destroy(Request $request, TicketType $ticketType)
    {
        $ticketType->delete();

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Ticket Type Deleted Successfully!'
        ]);

        return back();
    }
}
