<?php

namespace Tests\Feature\Customer;

use Crater\Models\Customer;
use Crater\Models\Expense;
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

test('get customer expenses', function () {
    $customer = Auth::guard('customer')->user();

    getJson("api/v1/{$customer->company->slug}/customer/expenses?page=1")->assertOk();
});

test('get customer expense', function () {
    $customer = Auth::guard('customer')->user();

    $expense = Expense::factory()->create([
        'customer_id' => $customer->id,
        'company_id' => $customer->company->id
    ]);

    getJson("/api/v1/{$customer->company->slug}/customer/expenses/{$expense->id}")
        ->assertOk();
});
