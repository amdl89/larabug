<?php

namespace App\Http\Requests;

use App\Enums\ProjectStatus;
use App\Models\ProjectPriority;
use App\Rules\EqualOrAfterCreatedAt;
use App\Rules\SupervisorWithIdExists;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
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
            'status' => ['required', Rule::in(ProjectStatus::getKeys())],
            'deadline' => ['required', 'date'],
            'priority' => ['required', 'exists:' . ProjectPriority::class . ',id'],
            'supervisor' => ['sometimes', 'nullable', new SupervisorWithIdExists],
        ];
    }
}
