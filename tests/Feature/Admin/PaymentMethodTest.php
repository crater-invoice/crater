<?php

use Crater\Http\Controllers\V1\Admin\Payment\PaymentMethodsController;
use Crater\Http\Requests\PaymentMethodRequest;
use Crater\Models\PaymentMethod;
use Crater\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);

    $user = User::find(1);
    $this->withHeaders([
        'company' => $user->companies()->first()->id,
    ]);
    Sanctum::actingAs(
        $user,
        ['*']
    );
});

test('get payment methods', function () {
    $response = getJson('api/v1/payment-methods?page=1');

    $response->assertOk();
});

test('create payment method', function () {
    $data = [
        'name' => 'demo name',
        'company_id' => User::find(1)->companies()->first()->id,
    ];

    $response = postJson('api/v1/payment-methods', $data);

    $response->assertStatus(201);

    $this->assertDatabaseHas('payment_methods', [
        'name' => $data['name'],
        'company_id' => $data['company_id'],
    ]);
});

test('store validates using a form request', function () {
    $this->assertActionUsesFormRequest(
        PaymentMethodsController::class,
        'store',
        PaymentMethodRequest::class
    );
});

test('get payment method', function () {
    $method = PaymentMethod::factory()->create();

    $response = getJson("api/v1/payment-methods/{$method->id}");

    $response->assertOk();

    $this->assertDatabaseHas('payment_methods', [
        'id' => $method->id,
        'name' => $method['name'],
        'company_id' => $method['company_id'],
    ]);
});

test('update payment method', function () {
    $method = PaymentMethod::factory()->create();

    $data = [
        'name' => 'updated name',
    ];

    $response = putJson("api/v1/payment-methods/{$method->id}", $data);

    $response->assertOk();

    $this->assertDatabaseHas('payment_methods', [
        'id' => $method->id,
        'name' => $data['name'],
    ]);
});

test('update validates using a form request', function () {
    $this->assertActionUsesFormRequest(
        PaymentMethodsController::class,
        'update',
        PaymentMethodRequest::class
    );
});

test('delete payment method', function () {
    $method = PaymentMethod::factory()->create();

    $response = deleteJson('api/v1/payment-methods/'.$method->id);

    $response->assertOk();

    $this->assertDeleted($method);
});
