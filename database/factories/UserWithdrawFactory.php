<?php

use Faker\Generator as Faker;
use App\Models\UserWithdraw;

$factory->define(UserWithdraw::class, function (Faker $faker) {

    // 随机取一个周以内的时间
    $updated_at = $faker->dateTimeBetween($startDate = '-6 days', $endDate = 'now');
    // 传参为生成最大时间不超过，创建时间永远比更改时间要早
    $created_at = $faker->dateTimeBetween($startDate = '-6 days', $endDate = 'now');

    return [
        'user_id' => null,
        'type' => $faker->randomElement(array_keys(UserWithdraw::$TypeMap)),
        'status' => $faker->randomElement(array_keys(UserWithdraw::$StatusMap)),
        'money' => $faker->randomFloat(2, 10, 99),
        'info' => [],
        'reason' => null,

        'created_at' => $created_at,
        'updated_at' => $updated_at,
    ];
});
