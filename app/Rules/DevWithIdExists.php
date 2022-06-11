<?php

namespace App\Rules;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class DevWithIdExists implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        return User::role(UserRole::Dev)->where('id', $value)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Dev with given id :input doesn\'t exist';
    }
}
