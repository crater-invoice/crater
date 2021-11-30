<?php

use Crater\Models\Address;
use Crater\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);
});

test('user belongs to currency', function () {
    $user = User::factory()->create();

    $this->assertTrue($user->currency()->exists());
});

test('user has many addresses', function () {
    $user = User::factory()->hasAddresses(2)->create();

    $this->assertTrue($user->addresses()->exists());
});

test('user belongs to many companies', function () {
    $user = User::factory()->hasCompanies(5)->create();

    $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->companies);
});

it('user has one billing address', function () {
    $user = User::factory()->has(Address::factory()->state([
        'type' => Address::BILLING_TYPE,
    ]))->create();

    $this->assertTrue($user->billingAddress()->exists());
});

it('user has one shipping address', function () {
    $user = User::factory()->has(Address::factory()->state([
        'type' => Address::SHIPPING_TYPE,
    ]))->create();

    $this->assertTrue($user->shippingAddress()->exists());
});

test('create customer', function () {
    $customer = User::factory()->raw([
        'role' => 'customer',
    ]);

    $request = new Request();

    $request->replace($customer);

    $response = User::createCustomer($request);

    $this->assertDatabaseHas('users', [
        'name' => $customer['name'],
        'email' => $customer['email'],
        'role' => $customer['role'],
    ]);
});

test('update customer', function () {
    $customer = User::factory()->create([
        'role' => 'customer',
    ]);

    $customer2 = User::factory()->raw([
        'role' => 'customer',
    ]);

    $request = new Request();

    $request->replace($customer2);

    $updateCustomer = User::updateCustomer($request, $customer);

    $newCustomer = User::find($customer->id);

    $this->assertDatabaseHas('users', [
        'id' => $customer->id,
        'name' => $customer2['name'],
        'email' => $customer2['email'],
        'role' => $customer2['role'],
    ]);
});
