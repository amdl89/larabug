<?php

namespace App\Http\Controllers;

use App\Enums\ReceivedMessageStatus;
use App\Enums\ToastType;
use App\Http\Resources\MessageResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserTrashedReceivedMessageController extends Controller
{
    public function index(Request $request, User $user)
    {
        return MessageResource::collection(
            $msgs = $user->trashedReceivedMessages()
                ->withTimestamps()
                ->withPivot(['id', 'receivedStatus'])
                ->with([
                    'sender' => fn ($q1) => $q1->with(
                        ['profile' => fn ($q2) => $q2->with('media')->select('id', 'name')]
                    )
                        ->select('users.id')
                        ->withForeignKeys(),
                    'receipents' => fn ($q1) => $q1->with(
                        ['profile' => fn ($q2) => $q2->select('id', 'name')]
                    )
                        ->select('users.id')
                        ->withForeignKeys(),
                ])
                ->when(
                    $request->get('searchQuery'),
                    fn ($query, $searchQuery) => $query->searchAndOrder($searchQuery),
                    fn ($query) => $query->orderByLatestPivot('updated_at')
                )
                ->cursorPaginate()
        )
            ->additional([
                'meta' => [
                    'next_cursor' => optional($msgs->nextCursor())->encode(),
                ]
            ]);
    }

    public function storeAll(Request $request, User $user)
    {
        DB::transaction(function () use ($user)
        {
            $user->notInTrashMessageReceptions()
                ->where(
                    'receivedStatus',
                    ReceivedMessageStatus::Unread
                )
                ->update([
                    'receivedStatus' => ReceivedMessageStatus::UnreadTrashed
                ]);

            $user->notInTrashMessageReceptions()
                ->where(
                    'receivedStatus',
                    ReceivedMessageStatus::Read
                )
                ->update([
                    'receivedStatus' => ReceivedMessageStatus::ReadTrashed
                ]);
        });

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Moved All To Trash'
        ]);

        return back();
    }

    public function destroyAll(User $user)
    {
        $user->trashedMessageReceptions()->delete();

        return response()->noContent();
    }
}
