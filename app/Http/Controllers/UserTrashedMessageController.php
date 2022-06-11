<?php

namespace App\Http\Controllers;

use App\Http\Resources\MessageResource;
use App\Models\User;

class UserTrashedMessageController extends Controller
{
    public function index(User $user)
    {
        return inertia('Users/TrashedMessages/Index', [
            'receivedMessages' => fn () => MessageResource::collection(
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
                    ->orderByLatestPivot('updated_at')
                    ->cursorPaginate()
            )
                ->additional([
                    'meta' => [
                        'next_cursor' => optional($msgs->nextCursor())->encode(),
                    ]
                ]),

            'sentMessages' => fn () => MessageResource::collection(
                $msgs = $user->trashedSentMessages()
                    ->with([
                        'receipents' => fn ($q1) => $q1->with(
                            ['profile' => fn ($q2) => $q2->select('id', 'name')]
                        )
                            ->select('users.id')
                            ->withForeignKeys(),
                    ])
                    ->latestFirst('updated_at')
                    ->cursorPaginate()
            )
                ->additional([
                    'meta' => [
                        'next_cursor' => optional($msgs->nextCursor())->encode(),
                    ]
                ]),
        ]);
    }
}
