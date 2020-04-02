<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Crater\Payment;
use Crater\User;
use Faker\Generator as Faker;

$factory->define(Payment::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return factory(User::class)->create(['role' => 'customer'])->id;
        },
        'payment_date' => $faker->date($format = 'd/m/Y', $max = 'now'),
        'company_id' => User::find(1)->company_id,
        'notes' => $faker->text(80),
        'amount' => $faker->randomDigitNotNull,
        'payment_number' => 'PAY-'.Payment::getNextPaymentNumber(),
        'payment_mode' => 'OTHER'
    ];
});
