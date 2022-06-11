<?php

namespace App\Http\Controllers;

use App\Enums\SentMessageStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserRestoredSentMessageController extends Controller
{
    public function store(Request $request, User $user)
    {
        $message = $user->trashedSentMessages()
            ->whereKey($request->get('message'))
            ->firstOr(
                ['*'],
                function ()
                {
                    throw ValidationException::withMessages([
                        'message' => ['Trashed sent message with given id doesn\'t exist for current user']
                    ]);
                }
            );

        $message->update([
            'sentStatus' => SentMessageStatus::Sent,
        ]);

        return response()->noContent();
    }
}
