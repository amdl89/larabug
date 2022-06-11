<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
            'name' => ['required', 'between:5,100'],
            'title' => ['required', 'between:5,100'],
            "bio" => ['required', 'between:5,2000'],
            "address" => ['required', 'between:5,100'],
            "education" => ['required', 'between:5,100'],
        ];
    }
}
