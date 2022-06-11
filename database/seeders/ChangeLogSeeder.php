<?php

namespace Database\Seeders;

use App\Models\ChangeLog;
use Illuminate\Database\Seeder;

class ChangeLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ChangeLog::factory()->count(5)->create();
    }
}
