<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Laraspace\ExpenseCategory;
use Faker\Generator as Faker;
use Laraspace\User;

$factory->define(ExpenseCategory::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'company_id' => User::find(1)->company_id,
        'description' => $faker->text
    ];
});
