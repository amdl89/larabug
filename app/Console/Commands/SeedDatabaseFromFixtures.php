<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SeedDatabaseFromFixtures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:database-from-fixtures';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed database from fixture files';

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
        Artisan::call('migrate:fresh');

        Artisan::call('migrate:rollback', [
            '--step' => 1
        ]);

        $this->getDbTableNames()
            ->reject(fn ($table) => $table == 'migrations')
            ->each(
                function ($table)
                {
                    DB::table($table)->insert(
                        json_decode(Storage::disk('dbDump')->get("$table.json"), true)
                    );
                }
            );

        Artisan::call('migrate');

        return 0;
    }

    private function getDbTableNames()
    {
        return collect(
            DB::select('SHOW TABLES')
        )->map(fn ($val) => $val->{"Tables_in_" . env('DB_DATABASE')});
    }
}
