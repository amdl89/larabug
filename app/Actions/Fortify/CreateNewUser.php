<?php

namespace App\Actions\Fortify;

use App\Enums\UserRole;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique(User::class, 'username'),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
            'role' => ['required', Rule::in(UserRole::getValues(['Tester', 'Dev']))],
        ])
            ->validate();

        return DB::transaction(function () use ($input)
        {
            $profile = Profile::create([
                'name' => 'Annonymous',
                'title' => 'No Title Provided',
                "bio" => 'No Bio Provided',
                "address" => 'No Address Provided',
                "education" => 'No Education Provided',
            ]);

            $user = User::make([
                'username' => $input['username'],
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);

            $user->profile()->associate($profile)->save();
            $user->assignRole($input['role']);
        });
    }
}
