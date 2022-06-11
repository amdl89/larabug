<?php

namespace App\Http\Controllers;

use App\Enums\ReceivedMessageStatus;
use App\Http\Resources\MessageResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserNotInTrashReceivedMessageController extends Controller
{
    public function index(Request $request, User $user)
    {
        $msgQuery = $user->notInTrashReceivedMessages()
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
            ]);

        if ($request->wantsJson())
        {
            return MessageResource::collection(
                $msgs = $msgQuery->when(
                    $request->get('searchQuery'),
                    fn ($query, $searchQuery) => $query->searchAndOrder($searchQuery),
                    fn ($query) => $query->orderByLatestPivot()
                )
                    ->when(
                        $request->get('filters'),
                        fn ($query, $filters) => $query->filter($filters)
                    )
                    ->cursorPaginate()
            )
                ->additional([
                    'meta' => [
                        'next_cursor' => optional($msgs->nextCursor())->encode(),
                    ]
                ]);
        }

        return inertia('Users/ReceivedMessages/Index', [
            'initialMessages' => fn () => MessageResource::collection(
                $msgs = $msgQuery
                    ->orderByLatestPivot()
                    ->cursorPaginate()
            )
                ->additional([
                    'meta' => [
                        'next_cursor' => optional($msgs->nextCursor())->encode(),
                    ]
                ]),

            'users' => fn () => UserResource::collection(
                User::query()
                    ->where('id', '<>', $request->user()->id)
                    ->with(['profile' => fn ($q2) => $q2->select('id', 'name')])
                    ->select('id')
                    ->withForeignKeys()
                    ->get()
            ),
        ]);
    }

    public function storeAll(User $user)
    {
        DB::transaction(function () use ($user)
        {
            $user->trashedMessageReceptions()
                ->where(
                    'receivedStatus',
                    ReceivedMessageStatus::UnreadTrashed
                )
                ->update([
                    'receivedStatus' => ReceivedMessageStatus::Unread
                ]);

            $user->trashedMessageReceptions()
                ->where(
                    'receivedStatus',
                    ReceivedMessageStatus::ReadTrashed
                )
                ->update([
                    'receivedStatus' => ReceivedMessageStatus::Read
                ]);
        });

        return response()->noContent();
    }
}
