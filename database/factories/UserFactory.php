<?php

use Illuminate\Support\Str;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\User::class, function (Faker $faker) {

    // 随机取一个周以内的时间
    $updated_at = $faker->dateTimeBetween($startDate = '-6 days', $endDate = 'now');
    // 传参为生成最大时间不超过，创建时间永远比更改时间要早
    $created_at = $faker->dateTimeBetween($startDate = '-6 days', $endDate = 'now');

    return [
        'wx_openid' => Str::random(),
        'name' => $faker->name,
        'gender' => $faker->randomElement(['男', '女']),
        'phone' => $faker->phoneNumber,
        'avatar' => $faker->imageUrl(),
        'money' => 0,

        'country' => $faker->country,
        'province' => $faker->country,
        'city' => $faker->city,

        'created_at' => $created_at,
        'updated_at' => $updated_at,
    ];
});
