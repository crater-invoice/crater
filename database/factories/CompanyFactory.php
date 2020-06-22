<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Crater\Company;
use Faker\Generator as Faker;

$factory->define(Company::class, function (Faker $faker) {
    return [
        'unique_hash' => str_random(60),
        'name' => $faker->name
    ];
});
