<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Crater\EstimateItem;
use Crater\Item;
use Faker\Generator as Faker;
use Crater\User;

$factory->define(EstimateItem::class, function (Faker $faker) {
    return [
        'item_id' => function () {
            return factory(Item::class)->create()->id;
        },
        'name' => function (array $item) {
            return Item::find($item['item_id'])->name;
        },
        'description' => function (array $item) {
            return Item::find($item['item_id'])->description;
        },
        'price' => function (array $item) {
            return Item::find($item['item_id'])->price;
        },
        'quantity' => $faker->randomDigitNotNull,
        'company_id' => User::find(1)->company_id,
        'discount_type' => 'fixed',
        'tax' => $faker->randomDigitNotNull,
        'discount_val' => 0,
        'total' => function (array $item) {
            return ($item['price'] * $item['quantity']);
        },
        'discount' => 0
    ];
});
