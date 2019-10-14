<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Recycler::class, function (Faker $faker) {
    // 随机取一个周以内的时间
    $updated_at = $faker->dateTimeBetween($startDate = '-6 days', $endDate = 'now');
    // 传参为生成最大时间不超过，创建时间永远比更改时间要早
    $created_at = $faker->dateTimeBetween($startDate = '-6 days', $endDate = 'now');

    return [
        'name' => $faker->name,
        'phone' => $faker->phoneNumber,
        'avatar' => url('defaults/recycler_avatar.png'),
        'money' => $faker->randomFloat(2, 100, 1000),
        'frozen_money' => 0,

        'password' => bcrypt('123456'),

        'created_at' => $created_at,
        'updated_at' => $updated_at,
    ];

});
