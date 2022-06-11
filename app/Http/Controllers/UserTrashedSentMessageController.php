<?php

namespace App\Http\Controllers;

use App\Enums\SentMessageStatus;
use App\Enums\ToastType;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use JustSteveKing\StatusCode\Http;

class UserTrashedSentMessageController extends Controller
{
    public function index(Request $request, User $user)
    {
        return MessageResource::collection(
            $msgs = $user->trashedSentMessages()
                ->with([
                    'receipents' => fn ($q1) => $q1->with(
                        ['profile' => fn ($q2) => $q2->select('id', 'name')]
                    )
                        ->select('users.id')
                        ->withForeignKeys(),
                ])
                ->when(
                    $request->get('searchQuery'),
                    fn ($query, $searchQuery) => $query->searchAndOrder($searchQuery),
                    fn ($query) => $query->latestFirst('updated_at')
                )
                ->cursorPaginate(),
        )
            ->additional([
                'meta' => [
                    'next_cursor' => optional($msgs->nextCursor())->encode(),
                ]
            ]);
    }

    public function store(Request $request, User $user, Message $message)
    {
        abort_unless(
            $user->notInTrashSentMessages()
                ->whereKey($message->id)
                ->exists(),
            Http::NOT_FOUND
        );

        $message->update([
            'sentStatus' => SentMessageStatus::Trashed
        ]);

        if ($request->wantsJson())
            return response()->noContent();

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Moved To Trash'
        ]);

        return back();
    }

    public function storeAll(Request $request, User $user)
    {
        $user->notInTrashSentMessages()
            ->update([
                'sentStatus' => SentMessageStatus::Trashed
            ]);

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Moved All To Trash'
        ]);

        return back();
    }

    public function destroy(User $user, Message $message)
    {
        abort_unless(
            $user->trashedSentMessages()
                ->whereKey($message->id)
                ->exists(),
            Http::NOT_FOUND
        );

        $message->delete();

        return response()->noContent();
    }

    public function destroyAll(User $user)
    {
        $user->trashedSentMessages()->delete();

        return response()->noContent();
    }
}
