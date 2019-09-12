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
        // 系统
        $this->call(AdminTablesSeeder::class);
        $this->call(ConfigsSeeder::class);

        // Banner
        $this->call(BannersSeeder::class);

        // 用户
        $this->call(UsersSeeder::class);

        // 回收员
        $this->call(RecyclersSeeder::class);

        // 服务站点
        $this->call(ServiceSitesSeeder::class);

        // 回收价格
        $this->call(ClientPricesSeeder::class);
        $this->call(RecyclePricesSeeder::class);

        // 箱
        $this->call(BinsSeeder::class);

        // 话题
        $this->call(TopicsSeeder::class);

        // 客户端订单
        $this->call(ClientOrdersSeeder::class);



        // 回收端订单
        $this->call(CleanOrdersSeeder::class);
    }
}
