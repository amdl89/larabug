<?php

namespace App\Http\Requests;

use App\Enums\DateRange;
use App\Enums\TicketStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;

class FilterTicketRequest extends FormRequest
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

    protected function getRedirectUrl()
    {
        return URL::current();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'page' => ['sometimes', 'integer'],

            'searchQuery' => ['sometimes', 'min:4'],

            'sortBy' => ['sometimes', 'nullable', 'array:field,order'],

            'sortBy.field' => [
                Rule::in([
                    'title',
                    'status',
                    'updatedAt',
                    'creator.profile.name',
                    'assignee.profile.name',
                    'priority.name'
                ]),
            ],

            'sortBy.order' => [Rule::in(['asc', 'desc'])],

            'filters' => [
                'sometimes',
                'array:devs,submitters,priorities,types,statuses,updatedRange,projects'
            ],

            'filters.devs' => ['sometimes', 'array'],
            'filters.submitters' => ['sometimes', 'array'],
            'filters.priorities' => ['sometimes', 'array'],
            'filters.types' => ['sometimes', 'array'],
            'filters.statuses' => ['sometimes', 'array'],
            'filters.updatedRange' => ['sometimes', 'nullable', Rule::in(DateRange::getValues())],
            'filters.projects' => ['sometimes', 'array'],

            'filters.*.devs' => ['distinct:strict'],
            'filters.*.submitters' => ['distinct:strict'],
            'filters.*.priorities' => ['distinct:strict'],
            'filters.*.types' => ['distinct:strict'],
            'filters.*.statuses' => ['distinct:strict', Rule::in(TicketStatus::getKeys())],
            'filters.*.projects' => ['distinct:strict'],

        ];
    }
}
