<?php

use Faker\Generator as Faker;

$factory->define(\App\Models\BinTypeHarmful::class, function (Faker $faker) {
    return [
        'name' => \App\Models\BinTypeHarmful::NAME,
        'status' => $faker->randomElement(array_keys(\App\Models\BinTypeHarmful::$StatusMap)),
        'number' => $faker->randomFloat(2, 1, 100),
        'unit' => '公斤',
    ];
});
