<?php

namespace App\Http\Requests;

use App\Enums\DateRange;
use App\Enums\ProjectStatus;
use App\Enums\TicketStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;

class FilterProjectsRequest extends FormRequest
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
                    'deadline',
                    'createdAt',
                    'priority.name',
                    'supervisor.profile.name',
                ]),
            ],

            'sortBy.order' => [Rule::in(['asc', 'desc'])],

            'filters' => [
                'sometimes',
                'array:status,priorities,createdRange,supervisors'
            ],

            'filters.status' => ['sometimes', 'nullable', Rule::in(ProjectStatus::getKeys())],
            'filters.priorities' => ['sometimes', 'array'],
            'filters.supervisors' => ['sometimes', 'array'],
            'filters.createdRange' => ['sometimes', 'nullable', Rule::in(DateRange::getValues())],

            'filters.*.priorities' => ['distinct:strict'],
            'filters.*.supervisors' => ['distinct:strict'],
        ];
    }
}
