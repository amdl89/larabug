<?php

namespace App\Http\Controllers;

use App\Enums\ReceivedMessageStatus;
use App\Enums\ToastType;
use App\Models\User;
use Illuminate\Http\Request;

class UserReadReceivedMessageController extends Controller
{
    public function storeAll(Request $request, User $user)
    {
        $user->notInTrashMessageReceptions()
            ->where(
                'receivedStatus',
                ReceivedMessageStatus::Unread
            )
            ->update([
                'receivedStatus' => ReceivedMessageStatus::Read
            ]);

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Marked All As Read'
        ]);

        return back();
    }
}
