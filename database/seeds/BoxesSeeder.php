<?php

use App\Models\Box;
use App\Models\ServiceSite;
use Illuminate\Database\Seeder;
use App\Models\BoxAdminUser;

class BoxesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run()
    {
        //回收员
        $box_admin_user = \Encore\Admin\Auth\Database\Role::where('slug','box_admin')->first()->administrators->first();

        // 青岛站
        $site = ServiceSite::where('city', '青岛市')->first();
        $lat = '36.092550';
        $lng = '120.381420';
        for ($i = 1; $i <= 20; $i++)
        {
            $box = factory(Box::class)->create([
                'site_id' => $site->id,
                'no' => 'CM053200' . $i,
                'lat' => $lat,
                'lng' => $lng,
            ]);
            $lat = bcsub($lat, '0.001', 6);
            $lng = bcsub($lng, '0.001', 6);

            // 回收箱绑定回收员
            if ($i <= 3)
            {
                BoxAdminUser::create([
                    'box_id' => $box->id,
                    'admin_user_id' => $box_admin_user->id,
                ]);
            }
        }

        // 济南站
        $site = ServiceSite::where('city', '济南市')->first();
        $lat = '36.660958';
        $lng = '117.016158';
        for ($i = 1; $i <= 5; $i++)
        {
            $box = factory(Box::class)->create([
                'site_id' => $site->id,
                'no' => 'CM053100' . $i,
                'lat' => $lat,
                'lng' => $lng,
            ]);
            $lat = bcsub($lat, '0.001', 6);
            $lng = bcsub($lng, '0.001', 6);

            // 回收箱绑定回收员
            if ($i <= 2)
            {
                BoxAdminUser::create([
                    'box_id' => $box->id,
                    'admin_user_id' => $box_admin_user->id,
                ]);
            }
        }
    }
}
