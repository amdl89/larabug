<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\Project;
use App\Models\ProjectPriority;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Storage::disk('attachedFile')->delete(Storage::disk('attachedFile')->allFiles());
        collect(Storage::disk('attachedFile')->directories())
            ->each(fn ($dir) => Storage::disk('attachedFile')->deleteDirectory($dir));

        Storage::disk('avatar')->delete(Storage::disk('avatar')->allFiles());
        collect(Storage::disk('avatar')->directories())
            ->each(fn ($dir) => Storage::disk('avatar')->deleteDirectory($dir));

        Storage::disk('projectCoverImage')->delete(Storage::disk('projectCoverImage')->allFiles());
        collect(Storage::disk('projectCoverImage')->directories())
            ->each(fn ($dir) => Storage::disk('projectCoverImage')->deleteDirectory($dir));

        collect([
            User::class,
            Project::class,
            Ticket::class,
            Message::class,
            TicketPriority::class,
            TicketType::class,
            ProjectPriority::class
        ])
            ->each(fn ($model) =>  Artisan::call('scout:flush', [
                'model' => $model
            ]));

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
            'model' => Ticket::class
        ]);

        Artisan::call('scout:import', [
            'model' => Message::class,
        ]);
    }
}
