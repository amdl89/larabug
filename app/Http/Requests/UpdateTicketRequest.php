<?php

namespace App\Http\Requests;

use App\Enums\TicketStatus;
use App\Models\TicketPriority;
use App\Models\TicketType;
use App\Rules\DevWithIdExists;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTicketRequest extends FormRequest
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
            'title' => ['required', 'between:5,100'],
            'description' => ['required', 'between:5,2000'],
            'status' => ['required', Rule::in(TicketStatus::getKeys())],
            'priority' => ['required', 'exists:' . TicketPriority::class . ',id',],
            'type' => ['required', 'exists:' . TicketType::class . ',id',],
            'assignee' => ['sometimes', 'nullable', new DevWithIdExists,],
        ];
    }
}
