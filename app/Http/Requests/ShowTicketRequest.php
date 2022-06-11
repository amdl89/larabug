<?php

namespace App\Http\Requests;

use App\Enums\DateRange;
use App\Enums\TicketProperty;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShowTicketRequest extends FormRequest
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
            'attachmentPage' => ['sometimes', 'integer'],

            'ticketChangeLogPage' => ['sometimes', 'integer'],

            'ticketChangeLogSortOrder' => ['sometimes', 'nullable', Rule::in(['asc', 'desc']),],

            'ticketChangeLogFilters' => [
                'sometimes',
                'array:property,initiator,dateRange'
            ],
            'ticketChangeLogFilters.property' => ['sometimes', 'nullable', Rule::in(TicketProperty::getValues())],
            'ticketChangeLogFilters.initiator' => ['sometimes', 'nullable', 'exists:users,id'],
            'ticketChangeLogFilters.dateRange' => ['sometimes', 'nullable', Rule::in(DateRange::getValues())],

        ];
    }
}
