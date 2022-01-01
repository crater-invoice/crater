<?php

use Crater\Models\Address;
use Crater\Models\Customer;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);
});

test('customer has many estimates', function () {
    $customer = Customer::factory()->hasEstimates(5)->create();

    $this->assertCount(5, $customer->estimates);

    $this->assertTrue($customer->estimates()->exists());
});

test('customer has many expenses', function () {
    $customer = Customer::factory()->hasExpenses(5)->create();

    $this->assertCount(5, $customer->expenses);

    $this->assertTrue($customer->expenses()->exists());
});

test('customer has many invoices', function () {
    $customer = Customer::factory()->hasInvoices(5)->create();

    $this->assertCount(5, $customer->invoices);

    $this->assertTrue($customer->invoices()->exists());
});

test('customer has many payments', function () {
    $customer = Customer::factory()->hasPayments(5)->create();

    $this->assertCount(5, $customer->payments);

    $this->assertTrue($customer->payments()->exists());
});

test('customer has many addresses', function () {
    $customer = Customer::factory()->hasAddresses(5)->create();

    $this->assertCount(5, $customer->addresses);

    $this->assertTrue($customer->addresses()->exists());
});

test('customer belongs to currency', function () {
    $customer = Customer::factory()->create();

    $this->assertTrue($customer->currency()->exists());
});

test('customer belongs to company', function () {
    $customer = Customer::factory()->forCompany()->create();

    $this->assertTrue($customer->company()->exists());
});

it('customer has one billing address', function () {
    $customer = Customer::factory()->has(Address::factory()->state([
        'type' => Address::BILLING_TYPE,
    ]))->create();

    $this->assertTrue($customer->billingAddress()->exists());
});

it('customer has one shipping address', function () {
    $customer = Customer::factory()->has(Address::factory()->state([
        'type' => Address::SHIPPING_TYPE,
    ]))->create();

    $this->assertTrue($customer->shippingAddress()->exists());
});
