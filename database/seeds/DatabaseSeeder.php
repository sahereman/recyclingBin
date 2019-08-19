<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //系统
        $this->call(AdminTablesSeeder::class);
        $this->call(ConfigsSeeder::class);
    }
}
