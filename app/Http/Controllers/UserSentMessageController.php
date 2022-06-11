<?php

namespace App\Http\Controllers;

use App\Enums\SentMessageStatus;
use App\Enums\ToastType;
use App\Http\Requests\StoreMessageRequest;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserSentMessageController extends Controller
{
    public function store(StoreMessageRequest $request, User $user)
    {
        DB::transaction(function () use ($request, $user)
        {
            $message = Message::make([
                'subject' => $request->get('subject'),
                'body' => $request->get('body'),
                'sentStatus' => SentMessageStatus::fromKey(
                    $request->get('sentStatus')
                )->value,
            ]);

            $message->sender()->associate($user);
            $message->save();

            $message->addRecepients($request->get('recepients'));
        });

        $request->session()->flash('toast', [
            'type' => ToastType::Success,
            'message' => $this->toastMessage(
                $request->get('sentStatus')
            )
        ]);

        return back();
    }

    private function toastMessage($sentStatusKey)
    {
        return $sentStatusKey == SentMessageStatus::Sent()->key ?
            'Message Sent Successfully' :
            'Message Added To Draft Successfully';
    }
}
