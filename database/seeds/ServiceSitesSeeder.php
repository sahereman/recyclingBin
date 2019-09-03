<?php

use App\Models\ServiceSite;
use Illuminate\Database\Seeder;

class ServiceSitesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ServiceSite::create([
            'name' => '青岛站',
            'county' => '中国',
            'province' => '山东省',
            'province_simple' => '山东',
            'city' => '青岛市',
            'city_simple' => '青岛',
        ]);

        ServiceSite::create([
            'name' => '济南站',
            'county' => '中国',
            'province' => '山东省',
            'province_simple' => '山东',
            'city' => '济南市',
            'city_simple' => '济南',
        ]);
    }
}
