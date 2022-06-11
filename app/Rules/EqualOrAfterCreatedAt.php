<?php

namespace App\Rules;

use App\Enums\ProjectStatus;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class EqualOrAfterCreatedAt implements Rule
{
    protected Model $model;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(Model $model, ?string $message = null)
    {
        $this->model = $model;
        $this->message = $message;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return Carbon::make($value)->greaterThanOrEqualTo($this->model->created_at);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message ?? 'Given date :input is less than given Model\'s created date';
    }
}
