<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Crater\Address;
use Faker\Generator as Faker;

$factory->define(Address::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'address_street_1' => $faker->streetAddress,
        'address_street_2' => $faker->streetAddress,
        'city_id' => 5909,
        'state_id' => 42,
        'country_id' => 1,
        'zip' => $faker->postcode,
        'phone' => $faker->phoneNumber,
        'fax' => $faker->phoneNumber,
        'type' => Address::BILLING_TYPE
    ];
});
