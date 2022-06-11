<?php

namespace Database\Seeders;

use App\Models\TicketPriority;
use DB;
use Illuminate\Database\Seeder;

class TicketPrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::transaction(
            function ()
            {
                TicketPriority::factory()->sampleValues()->each(
                    function ($tpColor, $tpName)
                    {
                        TicketPriority::factory()->create(['name' => $tpName, 'color' => $tpColor]);
                    }
                );
            }
        );
    }
}
