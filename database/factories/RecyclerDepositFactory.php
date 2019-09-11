<?php

use Faker\Generator as Faker;

$factory->define(App\Models\RecyclerDeposit::class, function (Faker $faker) {
    // 随机取一个周以内的时间
    $updated_at = $faker->dateTimeBetween($startDate = '-6 days', $endDate = 'now');
    // 传参为生成最大时间不超过，创建时间永远比更改时间要早
    $created_at = $faker->dateTimeBetween($startDate = '-6 days', $endDate = 'now');

    return [
        'recycler_id' => null,
        'method' => $faker->randomElement(array_keys(\App\Models\RecyclerDeposit::$MethodMap)),
        'status' => $faker->randomElement(array_keys(\App\Models\RecyclerDeposit::$StatusMap)),
        'money' => $faker->randomNumber(2),
        'payment_sn' => null,
        'paid_at' => null,
        'created_at' => $created_at,
        'updated_at' => $updated_at,
    ];
});
