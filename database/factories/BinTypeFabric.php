<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\BinTypeFabric::class, function (Faker $faker) {
    return [
        'name' => \App\Models\BinTypeFabric::NAME,
        'status' => $faker->randomElement(array_keys(\App\Models\BinTypeFabric::$StatusMap)),
        'number' => $faker->randomFloat(2, 1, 100),
        'unit' => '公斤',
        'threshold' => 100.00,
    ];
});
