<?php

namespace Database\Seeders;

use App\Models\TicketType;
use DB;
use Illuminate\Database\Seeder;

class TicketTypeSeeder extends Seeder
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
            TicketType::factory()->sampleValues()->each(
                function ($ttColor, $ttName)
                {
                    TicketType::factory()->create(['name' => $ttName, 'color' => $ttColor]);
                }
            );
        });
    }
}
