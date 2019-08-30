<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\BinTypePaper::class, function (Faker $faker) {
    return [
        'name' => \App\Models\BinTypePaper::NAME,
        'status' => $faker->randomElement(array_keys(\App\Models\BinTypePaper::$StatusMap)),
        'number' => $faker->randomFloat(2, 1, 100),
        'unit' => '公斤',
    ];
});
