<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Profile;
use App\Models\User;
use DB;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function ()
        {
            Profile::all()->each(
                fn (Profile $profile) => User::factory()->for($profile, 'profile')->create()
            );

            User::take(2)->get()->each(
                fn (User $user) => $user->assignRole(UserRole::Admin)
            );

            User::skip(2)->take(3)->get()->each(
                fn (User $user) => $user->assignRole(UserRole::PM)
            );

            User::skip(5)->take(8)->get()->each(
                fn (User $user) => $user->assignRole(UserRole::Dev)
            );

            User::skip(13)->take(7)->get()->each(
                fn (User $user) => $user->assignRole(UserRole::Tester)
            );

            User::whereRole(UserRole::Admin)->first()->update([
                'username' => 'demo-admin'
            ]);

            User::whereRole(UserRole::PM)->first()->update([
                'username' => 'demo-pm'
            ]);

            User::whereRole(UserRole::Dev)->first()->update([
                'username' => 'demo-dev'
            ]);

            User::whereRole(UserRole::Tester)->first()->update([
                'username' => 'demo-tester'
            ]);
        });
    }
}
