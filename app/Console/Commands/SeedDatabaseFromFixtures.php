<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SeedDatabaseFromFixtures extends Command {
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
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $this->call('migrate:fresh', [
            '--force' => true,
        ]);

        $this->call('migrate:rollback', [
            '--step' => 1,
            '--force' => true,
        ]);

        $this->getDbTableNames()
            ->each(
                function ($table) {
                    $rows = json_decode(Storage::disk('dbDump')->get("$table.json"), true);

                    // remove id column so that it can be generated from db
                    foreach ($rows as &$row) {
                        unset($row['id']);
                    }

                    DB::table($table)->insert(
                        $rows
                    );
                }
            );

        $this->call('migrate', [
            '--force' => true,
        ]);

        return 0;
    }

    private function getDbTableNames() {
        return collect([
            0 => "attachments",
            1 => "change_logs",
            2 => "comments",
            3 => "failed_jobs",
            4 => "jobs",
            5 => "media",
            6 => "messages",
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
