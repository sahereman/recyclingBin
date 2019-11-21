<?php

use App\Models\Config;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class ConfigsSeeder extends Seeder
{
    private $config_groups =
        [
            //站点设置
            [
                'name' => '站点设置',
                'type' => "group",
                'sort' => 1000,
                'configs' =>
                    [
                        // ['name' => '网站标题', 'code' => 'title', 'type' => "text", 'sort' => 10, 'value' => '网站标题', 'help' => '默认的网站SEO标题',],
                        // ['name' => '网站关键字', 'code' => 'keywords', 'type' => "text", 'sort' => 20],
                        // ['name' => '网站描述', 'code' => 'description', 'type' => "text", 'sort' => 30],
                        // ['name' => '司机端 Android Apk', 'code' => 'driver_android_apk', 'type' => "file", 'sort' => 40],
                        // ['name' => '小程序二维码', 'code' => 'client_qrcode', 'type' => "image", 'sort' => 50],

                        // ['name' => '网站关闭', 'code' => 'site_close', 'type' => "radio", 'sort' => 50,
                        //     'select_range' => [['value' => 0, 'name' => '开启'], ['value' => 1, 'name' => '关闭']],
                        //     'help' => '网站开启临时维护时,请关闭站点',
                        // ],
                    ]
            ],

            //传统箱投递奖励
            [
                'name' => '传统箱投递奖励',
                'type' => "group",
                'sort' => 1000,
                'configs' =>
                    [
                        ['name' => '奖励周期(天)', 'code' => 'box_order_profit_day', 'type' => "text", 'sort' => 10, 'value' => '7'],
                        ['name' => '奖励周期内可获得几次奖励', 'code' => 'box_order_profit_number', 'type' => "text", 'sort' => 20, 'value' => '2'],
                        ['name' => '固定的奖励金额(元)', 'code' => 'box_order_profit_money', 'type' => "text", 'sort' => 30, 'value' => '0.2'],
                    ]
            ],

            // 回收垃圾箱设置
            [
                'name' => '回收垃圾箱设置',
                'type' => "group",
                'sort' => 2000,
                'configs' =>
                    [
                        ['name' => '纺织物垃圾箱阈值', 'code' => 'fabric_threshold', 'type' => "text", 'sort' => 10, 'value' => '100.00'],
                        ['name' => '可回收物垃圾箱阈值', 'code' => 'paper_threshold', 'type' => "text", 'sort' => 20, 'value' => '100.00'],
                    ]
            ],
        ];

    public function run()
    {
        Config::truncate();
        Cache::forget(Config::$cache_key);

        foreach ($this->config_groups as $item)
        {
            $group = Config::create(array_except($item, 'configs'));

            foreach ($item['configs'] as $config)
            {
                Config::create(array_merge($config, ['parent_id' => $group->id]));
            }
        }
    }
}
