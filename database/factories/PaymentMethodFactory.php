<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Crater\PaymentMethod;
use Crater\User;
use Faker\Generator as Faker;

$factory->define(PaymentMethod::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'company_id' => User::find(1)->company_id,
    ];
});
