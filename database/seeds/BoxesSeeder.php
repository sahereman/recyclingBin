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
        BoxAdminUser::truncate();

        //回收员
        $box_admin_user = \Encore\Admin\Auth\Database\Role::where('slug', 'box_admin')->first()->administrators->first();

        // 青岛站
        $site = ServiceSite::all()->first();
        $lat = '36.092550';
        $lng = '120.381420';
        for ($i = 1; $i <= 3973; $i++)
        {
            $box = factory(Box::class)->create([
                'site_id' => $site->id,
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
    }
}
