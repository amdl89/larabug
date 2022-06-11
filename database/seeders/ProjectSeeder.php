<?php

namespace Database\Seeders;

use App\Enums\ProjectStatus;
use App\Enums\UserRole;
use App\Models\Project;
use App\Models\ProjectPriority;
use App\Models\ProjectUser;
use App\Models\User;
use DB;
use Faker\Factory as Faker;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ProjectSeeder extends Seeder
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
            $projectProrities = ProjectPriority::all();

            Project::factory()->count(3)
                ->make([
                    'status' => ProjectStatus::Active
                ])
                ->each(function (Project $project) use ($projectProrities)
                {
                    $project->priority()->associate($projectProrities->random())->save();
                });

            Project::factory()->count(2)
                ->make([
                    'status' => ProjectStatus::Inactive
                ])
                ->each(function (Project $project) use ($projectProrities)
                {
                    $project->priority()->associate($projectProrities->random())->save();
                });

            $supervisors = User::query()
                ->canSuperviseAProject()
                ->get();

            Project::all()->each(function (Project $project) use ($supervisors)
            {
                $projectUser = new ProjectUser;
                $projectUser->project()->associate($project);
                $projectUser->user()->associate($supervisors->random());
                $projectUser->save();
            });

            $devs = User::role([UserRole::Dev])->get();

            Project::all()->each(function (Project $project) use ($devs)
            {
                $devs->random($this->faker->numberBetween(5, 7))
                    ->each(function (User $dev) use ($project)
                    {
                        $projectUser = new ProjectUser;
                        $projectUser->project()->associate($project);
                        $projectUser->user()->associate($dev);
                        $projectUser->save();
                    });
            });

            $testers = User::role([UserRole::Tester])->get();

            Project::all()->each(function (Project $project) use ($testers)
            {
                $testers->random($this->faker->numberBetween(3, 5))
                    ->each(function (User $tester) use ($project)
                    {
                        $projectUser = new ProjectUser;
                        $projectUser->project()->associate($project);
                        $projectUser->user()->associate($tester);
                        $projectUser->save();
                    });
            });

            $projectCoverImages = Storage::disk('sample')->allFiles('projectCoverImages');

            Project::all()->each(
                function (Project $project, $key) use ($projectCoverImages)
                {
                    $project
                        ->addMediaFromDisk($projectCoverImages[$key], 'sample')
                        ->preservingOriginal()
                        ->toMediaCollection('coverImage');
                }
            );
        });
    }
}
