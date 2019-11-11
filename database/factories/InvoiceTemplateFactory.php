<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Laraspace\InvoiceTemplate;
use Faker\Generator as Faker;

$factory->define(InvoiceTemplate::class, function (Faker $faker) {
    return [
        'path' => $faker->word,
        'view' => $faker->word,
        'name' => $faker->word,
    ];
});
