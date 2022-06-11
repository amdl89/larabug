<?php

namespace App\Http\Controllers;

use App\Enums\SentMessageStatus;
use App\Http\Resources\MessageResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserNotInTrashSentMessageController extends Controller
{
    public function index(Request $request, User $user)
    {
        $msgQuery = $user->notInTrashSentMessages()
            ->with([
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
                    fn ($query) => $query->latestFirst()
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

        return inertia('Users/SentMessages/Index', [
            'initialMessages' => fn () => MessageResource::collection(
                $msgs = $msgQuery
                    ->latestFirst()
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
        $user->trashedSentMessages()
            ->update([
                'sentStatus' => SentMessageStatus::Sent
            ]);

        return response()->noContent();
    }
}
