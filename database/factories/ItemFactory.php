<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Crater\Item;
use Crater\Tax;
use Faker\Generator as Faker;
use Crater\User;

$factory->define(Item::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'description' => $faker->text,
        'company_id' => User::find(1)->company_id,
        'price' => $faker->randomDigitNotNull,
        'unit' => 'kg'
    ];
});

$factory->afterCreating(Item::class, function ($item, $faker) {
    $item->taxes()->save(factory(Tax::class)->make());
    $item->taxes()->save(factory(Tax::class)->make());
});
