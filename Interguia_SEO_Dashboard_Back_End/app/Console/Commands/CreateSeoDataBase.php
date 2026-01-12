<?php

namespace App\Console\Commands;
use Illuminate\Support\Facades\DB;

use Illuminate\Console\Command;

class CreateSeoDataBase extends Command
{
   
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-seo-data-base';

    /**
     * The console command description.
     *
     * @var string
     */
   protected $description = 'Create Laravel app database if it does not exist';
    /**
     * Execute the console command.
     */
    public function handle()
    {
       $dbName = env('APP_DB_DATABASE');

        // Check if DB exists in SQL Server
        $exists = DB::connection('sqlsrv_master') // TEMP: use master connection if needed
            ->select("SELECT name FROM sys.databases WHERE name = ?", [$dbName]);

        if ($exists) {
            $this->info("Database '$dbName' already exists.");
        } else {
            // Create the new DB
            DB::connection('sqlsrv_master')->statement("CREATE DATABASE [$dbName]");
            $this->info("Database '$dbName' created successfully.");
        }

        return 0;
    
    }
}
