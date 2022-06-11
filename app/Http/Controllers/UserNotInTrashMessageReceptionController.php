<?php

namespace App\Http\Controllers;

use App\Enums\ReceivedMessageStatus;
use App\Models\User;
use App\Models\UserReceivedMessage;
use JustSteveKing\StatusCode\Http;

class UserNotInTrashMessageReceptionController extends Controller
{
    public function store(User $user, UserReceivedMessage $messageReception)
    {
        abort_unless(
            $user->trashedMessageReceptions()
                ->whereKey($messageReception->id)
                ->exists(),
            Http::NOT_FOUND
        );

        $messageReception->update([
            'receivedStatus' =>
            $messageReception->receivedStatus->is(ReceivedMessageStatus::UnreadTrashed) ?
                ReceivedMessageStatus::Unread :
                ReceivedMessageStatus::Read
        ]);

        return response()->noContent();
    }
}
