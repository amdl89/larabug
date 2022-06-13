<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CreateFixturesFromDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:fixtures-from-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create fixture files from contents of database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        File::cleanDirectory(storage_path('app/dbDumps'));

        $this->getDbTableNames()
            ->reject(fn ($table) => $table == 'migrations')
            ->each(
                function ($table)
                {
                    Storage::disk('dbDump')->put(
                        "$table.json",
                        DB::table($table)->get()->toJson()
                    );
                }
            );

        return 0;
    }

    private function getDbTableNames()
    {
        return collect(
            DB::select('SHOW TABLES')
        )->map(fn ($val) => $val->{"Tables_in_" . env('DB_DATABASE')});
    }
}
