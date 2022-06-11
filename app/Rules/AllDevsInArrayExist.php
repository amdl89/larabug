<?php

namespace App\Rules;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class AllDevsInArrayExist implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return User::query()
            ->whereRole(UserRole::Dev)
            ->whereIn('id', $value)
            ->count()
            === count($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'One or more devs don\'t exist';
    }
}
