<?php

namespace App\Http\Controllers;

use App\Enums\ReceivedMessageStatus;
use App\Enums\ToastType;
use App\Models\User;
use App\Models\UserReceivedMessage;
use Illuminate\Http\Request;
use JustSteveKing\StatusCode\Http;

class UserReadMessageReceptionController extends Controller
{
    public function store(Request $request, User $user, UserReceivedMessage $messageReception)
    {
        abort_unless(
            $user->unreadMessageReceptions()
                ->whereKey($messageReception->id)
                ->exists(),
            Http::NOT_FOUND
        );

        $messageReception->update([
            'receivedStatus' => ReceivedMessageStatus::Read
        ]);

        if ($request->wantsJson())
            return response()->noContent();

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Moved To Trash'
        ]);

        return back();
    }
}
