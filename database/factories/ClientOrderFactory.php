<?php

use App\Models\ClientOrder;
use Faker\Generator as Faker;

$factory->define(ClientOrder::class, function (Faker $faker) {
    // 随机取一个周以内的时间
    $updated_at = $faker->dateTimeBetween($startDate = '-6 days', $endDate = 'now');
    // 传参为生成最大时间不超过，创建时间永远比更改时间要早
    $created_at = $faker->dateTimeBetween($startDate = '-6 days', $endDate = 'now');

    return [
        'bin_id' => null,
        'user_id' => null,
        'status' => ClientOrder::STATUS_COMPLETED,
        'bin_snapshot' => [],
        'total' => $faker->randomFloat(2, 20, 99),
        'created_at' => $created_at,
        'updated_at' => $updated_at,
    ];
});
