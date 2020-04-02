<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Crater\InvoiceItem;
use Crater\Item;
use Faker\Generator as Faker;
use Crater\User;

$factory->define(InvoiceItem::class, function (Faker $faker) {
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
        'company_id' => User::find(1)->company_id,
        'quantity' => $faker->randomDigitNotNull,
        'discount_type' => 'fixed',
        'discount_val' => 0,
        'tax' => $faker->randomDigitNotNull,
        'total' => function (array $item) {
            return ($item['price'] * $item['quantity']);
        },
        'discount' => 0
    ];
});
