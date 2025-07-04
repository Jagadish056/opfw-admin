<?php

use Database\Seeders\ServerSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        // $this->call(UsersTableSeeder::class);
        // $this->call(LogsTableSeeder::class);
        $this->call(ServerSeeder::class);
    }

}
