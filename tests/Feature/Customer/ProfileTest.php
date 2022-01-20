<?php

namespace Tests\Feature\Customer;

use Crater\Http\Controllers\V1\Customer\General\ProfileController;
use Crater\Http\Requests\Customer\CustomerProfileRequest;
use Crater\Models\Customer;
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

test('update customer profile using a form request', function () {
    $this->assertActionUsesFormRequest(
        ProfileController::class,
        'updateProfile',
        CustomerProfileRequest::class
    );
});

test('update customer profile', function () {
    $customer = Auth::guard('customer')->user();

    $newCustomer = Customer::factory()->raw([
        'shipping' => [
            'name' => 'newName',
            'address_street_1' => 'address'
        ],
        'billing' => [
            'name' => 'newName',
            'address_street_1' => 'address'
        ]
    ]);

    postJson("api/v1/{$customer->company->slug}/customer/profile", $newCustomer)->assertOk();

    $this->assertDatabaseHas('customers', [
        'name' => $customer['name'],
        'email' => $customer['email']
    ]);
});

test('get customer', function () {
    $customer = Auth::guard('customer')->user();

    getJson("api/v1/{$customer->company->slug}/customer/me")->assertOk();
});
