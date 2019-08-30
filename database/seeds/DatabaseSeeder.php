<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * @return void
     */
    public function run()
    {
        //系统
        $this->call(AdminTablesSeeder::class);
        $this->call(ConfigsSeeder::class);

        //用户
        $this->call(UsersSeeder::class);

        //服务站点
        $this->call(ServiceSitesSeeder::class);

        //回收价格
        $this->call(ClientPricesSeeder::class);
        $this->call(RecyclePricesSeeder::class);

        //箱
        $this->call(BinsSeeder::class);
    }
}
