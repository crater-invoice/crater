<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Crater\Address;
use Faker\Generator as Faker;

$factory->define(Address::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'address_street_1' => $faker->streetAddress,
        'address_street_2' => $faker->streetAddress,
        'city' => $faker->city,
        'state' => $faker->state,
        'country_id' => 231,
        'zip' => $faker->postcode,
        'phone' => $faker->phoneNumber,
        'fax' => $faker->phoneNumber,
        'type' => Address::BILLING_TYPE
    ];
});
