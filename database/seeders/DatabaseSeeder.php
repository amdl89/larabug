<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        File::cleanDirectory(storage_path('app/attachedFiles'));
        File::cleanDirectory(storage_path('app/avatars'));
        File::cleanDirectory(storage_path('app/projectCoverImages'));
        File::cleanDirectory(storage_path('app/indexes'));

        // order is important
        $this->call([
            TicketPrioritySeeder::class,
            TicketTypeSeeder::class,
            ProjectPrioritySeeder::class,
            UserRoleSeeder::class,
            ProfileSeeder::class,
            UserSeeder::class,
            ProjectSeeder::class,
            TicketSeeder::class,
            CommentSeeder::class,
            AttachmentSeeder::class,
            MessageSeeder::class,
        ]);

        Artisan::call('scout:import', [
            'model' => 'App\Models\Ticket'
        ]);

        Artisan::call('scout:import', [
            'model' => 'App\Models\Message'
        ]);
    }
}
