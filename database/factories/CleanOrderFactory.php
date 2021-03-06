<?php

use App\Models\CleanOrder;
use Faker\Generator as Faker;

$factory->define(CleanOrder::class, function (Faker $faker) {
    // 随机取一个周以内的时间
    $updated_at = $faker->dateTimeBetween($startDate = '-6 days', $endDate = 'now');
    // 传参为生成最大时间不超过，创建时间永远比更改时间要早
    $created_at = $faker->dateTimeBetween($startDate = '-6 days', $endDate = 'now');

    return [
        'bin_id' => null,
        'recycler_id' => null,
        'status' => CleanOrder::STATUS_COMPLETED,
        'bin_snapshot' => [],
        'total' => $faker->randomFloat(2, 20, 99),
        'created_at' => $created_at,
        'updated_at' => $updated_at,
    ];
});
