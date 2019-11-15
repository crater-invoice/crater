<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Crater\Invoice;
use Crater\User;
use Crater\Tax;
use Crater\InvoiceItem;
use Crater\InvoiceTemplate;
use Faker\Generator as Faker;

$factory->define(Invoice::class, function (Faker $faker) {
    return [
        'invoice_date' => $faker->date($format = 'd/m/Y', $max = 'now'),
        'due_date' => $faker->date($format = 'd/m/Y', $max = 'now'),
        'invoice_number' => 'INV-'.Invoice::getNextInvoiceNumber(),
        'reference_number' => Invoice::getNextInvoiceNumber(),
        'user_id' => function () {
            return factory(User::class)->create(['role' => 'customer'])->id;
        },
        'invoice_template_id' => 1,
        'status' => Invoice::STATUS_DRAFT,
        'tax_per_item' => 'NO',
        'discount_per_item' => 'NO',
        'paid_status' => Invoice::STATUS_UNPAID,
        'company_id' => User::find(1)->company_id,
        'sub_total' => $faker->randomDigitNotNull,
        'discount' => 0,
        'discount_type' => 'fixed',
        'discount_val' => 0,
        'total' => $faker->randomDigitNotNull,
        'tax' => $faker->randomDigitNotNull,
        'due_amount' => function (array $invoice) {
            return $invoice['total'];
        },
        'notes' => $faker->text(80),
        'unique_hash' => str_random(60)
    ];
});

$factory->afterCreating(Invoice::class, function ($invoice, $faker) {
    $invoice->items()->save(factory(InvoiceItem::class)->make());
    $invoice->items()->save(factory(InvoiceItem::class)->make());
});

$factory->afterCreating(Invoice::class, function ($invoice, $faker) {
    $invoice->taxes()->save(factory(Tax::class)->make());
    $invoice->items()->save(factory(Tax::class)->make());
});
