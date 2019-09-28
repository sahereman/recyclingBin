<?php

use Faker\Generator as Faker;

$factory->define(App\Models\ClientOrderItem::class, function (Faker $faker) {
    return [
        'order_id' => null,
        'type_slug' => $faker->randomElement([\App\Models\BinTypePaper::SLUG, \App\Models\BinTypeFabric::SLUG]),
        'type_name' => $faker->randomElement([\App\Models\BinTypePaper::NAME, \App\Models\BinTypeFabric::NAME]),
        'number' => $faker->randomFloat(2, 0, 3),
        'unit' => '公斤',
        'subtotal' => $faker->randomFloat(2, 1, 10),
    ];
});