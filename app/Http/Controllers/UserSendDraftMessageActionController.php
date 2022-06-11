<?php

namespace App\Http\Controllers;

use App\Enums\ToastType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserSendDraftMessageActionController extends Controller
{
    public function __invoke(Request $request, User $user)
    {
        $message = $user->draftMessages()
            ->whereKey($request->get('message'))
            ->firstOr(
                ['*'],
                function ()
                {
                    throw ValidationException::withMessages([
                        'message' => ['Draft message with given id doesn\'t exist for current user']
                    ]);
                }
            );

        $message->sendDraft();

        if ($request->wantsJson())
            return response()->noContent();

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => 'Message Sent'
        ]);

        return back();
    }
}
