<?php

use Crater\Models\Address;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);
});

test('an address belongs to user', function () {
    $address = Address::factory()->forUser()->create();

    $this->assertTrue($address->user->exists());
});

test('an address belongs to country', function () {
    $address = Address::factory()->create();

    $this->assertTrue($address->country->exists());
});

test('an address belongs to customer', function () {
    $address = Address::factory()->forCustomer()->create();

    $this->assertTrue($address->customer()->exists());
});
