<?php

namespace Database\Seeders;

use App\Models\Attachment;
use App\Models\Project;
use App\Models\Ticket;
use Faker\Factory as Faker;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttachmentSeeder extends Seeder
{
    protected Generator $faker;

    public function __construct()
    {
        $this->faker = Faker::create();
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(function ()
        {
            Ticket::get()->each(
                function (Ticket $ticket)
                {
                    $users = $ticket->project->users()->get();

                    Attachment::factory()
                        ->count($this->faker->numberBetween(1, 4))
                        ->for($ticket, 'attachable')
                        ->make()
                        ->transform(
                            fn (Attachment $attachment) => $attachment->uploader()->associate($users->random())
                        )
                        ->each(fn (Attachment $attachment) => $attachment->save());
                }
            );

            Project::get()->each(
                function (Project $project)
                {
                    $users = $project->users()->get();

                    Attachment::factory()
                        ->count($this->faker->numberBetween(3, 12))
                        ->for($project, 'attachable')
                        ->make()
                        ->transform(
                            fn (Attachment $attachment) => $attachment->uploader()->associate($users->random())
                        )
                        ->each(fn (Attachment $attachment) => $attachment->save());
                }
            );

            Attachment::get()->each(
                function (Attachment $attachment)
                {
                    $attachment
                        ->addMediaFromDisk('samplePdf.pdf', 'sample')
                        ->preservingOriginal()
                        ->toMediaCollection('attachedFile');
                }
            );
        });
    }
}
