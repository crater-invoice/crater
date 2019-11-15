<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Crater\ExpenseCategory;
use Faker\Generator as Faker;
use Crater\User;

$factory->define(ExpenseCategory::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'company_id' => User::find(1)->company_id,
        'description' => $faker->text
    ];
});
