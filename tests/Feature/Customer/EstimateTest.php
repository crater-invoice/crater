<?php

namespace Tests\Feature\Customer;

use Crater\Models\Customer;
use Crater\Models\Estimate;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

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

test('get customer estimates', function () {
    $customer = Auth::guard('customer')->user();

    getJson("api/v1/{$customer->company->slug}/customer/estimates?page=1")->assertOk();
});

test('get customer estimate', function () {
    $customer = Auth::guard('customer')->user();

    $estimate = Estimate::factory()->create();

    getJson("/api/{$customer->company->slug}/v1/customer/estimates/{$estimate->id}")->assertOk();
});

test('customer estimate mark as accepted', function () {
    $customer = Auth::guard('customer')->user();

    $estimate = Estimate::factory()->create([
        'estimate_date' => '1988-07-18',
        'expiry_date' => '1988-08-18',
    ]);

    $status = [
        'status' => Estimate::STATUS_ACCEPTED,
    ];

    postJson("api/v1/{$customer->company->slug}/customer/estimate/{$estimate->id}/accept", $status)->assertOk();

    $estimate2 = Estimate::find($estimate->id);

    $this->assertEquals($estimate2->status, Estimate::STATUS_ACCEPTED);
});
