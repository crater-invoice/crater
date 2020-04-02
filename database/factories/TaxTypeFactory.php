<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Crater\TaxType;
use Faker\Generator as Faker;
use Crater\User;

$factory->define(TaxType::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'company_id' => User::find(1)->company_id,
        'percent' => $faker->randomDigitNotNull,
        'description' => $faker->text,
        'compound_tax' => 0,
        'collective_tax' => 0
    ];
});
