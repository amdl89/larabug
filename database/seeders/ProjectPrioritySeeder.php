<?php

namespace Database\Seeders;

use App\Models\ProjectPriority;
use DB;
use Illuminate\Database\Seeder;

class ProjectPrioritySeeder extends Seeder
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
            ProjectPriority::factory()->sampleValues()->each(
                function ($ppColor, $ppName)
                {
                    ProjectPriority::factory()->create(['name' => $ppName, 'color' => $ppColor]);
                }
            );
        });
    }
}
