<?php

use Crater\Models\Payment;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);
});

test('payment belongs to invoice', function () {
    $payment = Payment::factory()->forInvoice()->create();

    $this->assertTrue($payment->invoice()->exists());
});

test('payment belongs to customer', function () {
    $payment = Payment::factory()->forCustomer()->create();

    $this->assertTrue($payment->customer()->exists());
});

test('payment belongs to payment method', function () {
    $payment = Payment::factory()->forPaymentMethod()->create();

    $this->assertTrue($payment->paymentMethod()->exists());
});
