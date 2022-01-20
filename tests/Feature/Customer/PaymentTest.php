<?php

namespace Tests\Feature\Customer;

use Crater\Models\Customer;
use Crater\Models\Payment;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\getJson;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);

    $customer = Customer::factory()->create();

    Sanctum::actingAs(
        $customer,
        ['*'],
        'customer'
    );
});

test('get customer payments', function () {
    $customer = Auth::guard('customer')->user();

    getJson("api/v1/{$customer->company->slug}/customer/payments?page=1")->assertOk();
});

test('get customer payment', function () {
    $customer = Auth::guard('customer')->user();

    $payment = Payment::factory()->create([
        'customer_id' => $customer->id
    ]);

    getJson("/api/v1/{$customer->company->slug}/customer/payments/{$payment->id}")->assertOk();
});
