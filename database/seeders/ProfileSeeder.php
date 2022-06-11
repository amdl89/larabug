<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\User;
use DB;
use Illuminate\Database\Seeder;
use Storage;

class ProfileSeeder extends Seeder
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
            Profile::factory()->times(20)->create();

            $avatars = Storage::disk('sample')->allFiles('avatars');

            Profile::all()->each(
                function (Profile $profile, $key) use ($avatars)
                {
                    $profile
                        ->addMediaFromDisk($avatars[$key], 'sample')
                        ->preservingOriginal()
                        ->toMediaCollection('avatar');
                }
            );
        });
    }
}
