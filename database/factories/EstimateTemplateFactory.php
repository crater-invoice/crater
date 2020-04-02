<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Crater\EstimateTemplate;
use Faker\Generator as Faker;

$factory->define(EstimateTemplate::class, function (Faker $faker) {
    return [
        'path' => $faker->word,
        'view' => $faker->word,
        'name' => $faker->word,
    ];
});
