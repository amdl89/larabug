<?php

namespace App\Http\Requests;

use App\Enums\SentMessageStatus;
use App\Models\User;
use App\Rules\AllRecepientsInArrayExist;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMessageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'subject' => ['required', 'between:5,200'],
            'body' => ['required', 'between:5,2000'],
            'sentStatus' => [
                'required',
                Rule::in(
                    SentMessageStatus::getKeys([
                        SentMessageStatus::Sent, SentMessageStatus::Draft
                    ])
                )
            ],
            'recepients' => [
                'required',
                'array',
                new AllRecepientsInArrayExist,
                function ($attribute, $value, $fail)
                {
                    if (
                        collect($value)->contains(
                            fn ($id) => $this->route('user')->id == $id
                        )
                    )
                    {
                        $fail('Recepient cannot be current user');
                    }
                },
            ],
        ];
    }
}
