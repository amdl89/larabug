<?php

namespace App\Http\Controllers;

use App\Enums\ReceivedMessageStatus;
use App\Enums\ToastType;
use App\Models\User;
use App\Models\UserReceivedMessage;
use Illuminate\Http\Request;
use JustSteveKing\StatusCode\Http;

class UserTrashedMessageReceptionController extends Controller
{
    public function store(Request $request, User $user, UserReceivedMessage $messageReception)
    {
        abort_unless(
            $user->notInTrashMessageReceptions()
                ->whereKey($messageReception->id)
                ->exists(),
            Http::NOT_FOUND
        );

        $messageReception->update([
            'receivedStatus' =>
            $messageReception->receivedStatus->is(ReceivedMessageStatus::Unread) ?
                ReceivedMessageStatus::UnreadTrashed :
                ReceivedMessageStatus::ReadTrashed
        ]);

        if ($request->wantsJson())
            return response()->noContent();

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Moved To Trash'
        ]);

        return back();
    }

    public function destroy(User $user, UserReceivedMessage $messageReception)
    {
        abort_unless(
            $user->trashedMessageReceptions()
                ->whereKey($messageReception->id)
                ->exists(),
            Http::NOT_FOUND
        );

        $messageReception->delete();

        return response()->noContent();
    }
}
