<?php

use Faker\Generator as Faker;

$factory->define(App\Models\CleanOrderItem::class, function (Faker $faker) {
    return [
        'order_id' => null,
        'type_name' => $faker->randomElement([\App\Models\BinTypePaper::NAME, \App\Models\BinTypeFabric::NAME]),
        'number' => $faker->randomFloat(2, 0, 3),
        'unit' => '公斤',
        'subtotal' => $faker->randomFloat(2, 1, 10),
    ];
});
