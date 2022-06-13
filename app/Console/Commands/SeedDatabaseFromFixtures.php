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
        return collect([
            0 => "attachments",
            1 => "change_logs",
            2 => "comments",
            3 => "failed_jobs",
            4 => "jobs",
            5 => "media",
            6 => "messages",
            7 => "migrations",
            8 => "model_has_permissions",
            9 => "model_has_roles",
            10 => "password_resets",
            11 => "permissions",
            12 => "profiles",
            13 => "project_priorities",
            14 => "project_users",
            15 => "projects",
            16 => "role_has_permissions",
            17 => "roles",
            18 => "ticket_priorities",
            19 => "ticket_types",
            20 => "tickets",
            21 => "user_received_messages",
            22 => "users",
        ]);
    }
}
