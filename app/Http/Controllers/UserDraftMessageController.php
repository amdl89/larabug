<?php

namespace App\Http\Controllers;

use App\Enums\ToastType;
use App\Http\Requests\UpdateDraftMessageRequest;
use App\Http\Resources\MessageResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JustSteveKing\StatusCode\Http;

class UserDraftMessageController extends Controller
{
    public function index(Request $request, User $user)
    {
        $msgQuery = $user->draftMessages()
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
                    ->cursorPaginate()
            )
                ->additional([
                    'meta' => [
                        'next_cursor' => optional($msgs->nextCursor())->encode(),
                    ]
                ]);
        }

        return inertia('Users/DraftMessages/Index', [
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

            'allRecepients' => fn () => UserResource::collection(
                User::query()
                    ->where('id', '<>', $request->user()->id)
                    ->with(['profile' => fn ($q2) => $q2->select('id', 'name')])
                    ->select('id')
                    ->withForeignKeys()
                    ->get()
            ),
        ]);
    }

    public function update(UpdateDraftMessageRequest $request, User $user, $message)
    {
        abort_unless(
            $msg = $user->draftMessages()
                ->whereKey($message)
                ->first(),
            Http::NOT_FOUND
        );

        DB::transaction(
            function () use ($request, $msg)
            {
                $msg->update([
                    'subject' => $request->get('subject'),
                    'body' => $request->get('body'),
                ]);

                $msg->syncRecepients($request->get('recepients'));
            }
        );

        return new MessageResource($msg->refresh()->load('receipents.profile'));
    }

    public function destroyAll(Request $request, User $user)
    {
        $user->draftMessages()->delete();

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Deleted All Drafts Successfully'
        ]);

        return back();
    }
}
