<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Banner::class, function (Faker $faker) {
    return [
        'slug' => \App\Models\Banner::SLUG_MINI,
        'image' => $faker->imageUrl(),
        'sort' => $faker->randomNumber(2),
    ];
});
