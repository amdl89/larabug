<?php

namespace App\Http\Requests;

use App\Models\ProjectPriority;
use App\Rules\SupervisorWithIdExists;
use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
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
            'deadline' => ['required', 'date', 'after_or_equal:today'],
            'priority' => ['required', 'exists:' . ProjectPriority::class . ',id'],
            'supervisor' => ['required', 'nullable', new SupervisorWithIdExists],
            'coverImage' => ['sometimes', 'nullable', 'file', 'image', 'max:10240'],
        ];
    }
}
