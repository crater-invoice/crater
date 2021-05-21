<?php

use Crater\Models\Payment;
use Crater\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'UnitSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'PaymentMethodSeeder', '--force' => true]);

    $user = User::where('role', 'super admin')->first();
    $this->withHeaders([
        'company' => $user->company_id,
    ]);
    Sanctum::actingAs(
        $user,
        ['*']
    );
});

test('payment belongs to invoice', function () {
    $payment = Payment::factory()->forInvoice()->create();

    $this->assertTrue($payment->invoice()->exists());
});


test('payment belongs to user', function () {
    $payment = Payment::factory()->create();

    $this->assertTrue($payment->user()->exists());
});

test('payment belongs to payment method', function () {
    $payment = Payment::factory()->forPaymentMethod()->create();

    $this->assertTrue($payment->paymentMethod()->exists());
});

test('get payment num attribute', function () {
    $payment = Payment::factory()->create();

    $num_attribute = $payment->getPaymentNumAttribute();

    $prefix_attribute = $payment->getPaymentPrefixAttribute();

    $this->assertEquals($prefix_attribute.'-'.$num_attribute, $payment['payment_number']);
});

test('get payment prefix attribute', function () {
    $payment = Payment::factory()->create();

    $num_attribute = $payment->getPaymentNumAttribute();

    $prefix_attribute = $payment->getPaymentPrefixAttribute();

    $this->assertEquals($prefix_attribute.'-'.$num_attribute, $payment['payment_number']);
});

test('get next payment number', function () {
    $payment = Payment::factory()->create();

    $prefix_attribute = $payment->getPaymentPrefixAttribute();

    $next_payment_number = $payment->getNextPaymentNumber($prefix_attribute);

    $payment2 = Payment::factory()->create();

    $this->assertEquals($prefix_attribute.'-'.$next_payment_number, $payment2['payment_number']);
});
