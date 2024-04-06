<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateDatabaseCommand extends Command
{
    protected $signature = 'db:create {database}';

    protected $description = 'Create a new database';

    public function handle()
    {
        $databaseName = $this->argument('database');

        try {
            DB::connection()->statement("CREATE DATABASE IF NOT EXISTS $databaseName");
            $this->info("Database $databaseName created successfully.");
        } catch (\Exception $e) {
            $this->error("Error creating database: " . $e->getMessage());
        }
    }
}
