<?php

use Crater\Http\Controllers\V1\Admin\ExchangeRate\ExchangeRateProviderController;
use Crater\Http\Requests\ExchangeRateProviderRequest;
use Crater\Models\ExchangeRateProvider;
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

test('get exchange rate providers', function () {
    getJson('api/v1/exchange-rate-providers?page=1')
        ->assertOk();
});

test('store user using a form request', function () {
    $this->assertActionUsesFormRequest(
        ExchangeRateProviderController::class,
        'store',
        ExchangeRateProviderRequest::class
    );
});

test('store recurring invoice', function () {
    $exchangeRateProvider = ExchangeRateProvider::factory()->raw();

    postJson('api/v1/exchange-rate-providers', $exchangeRateProvider)
        ->assertStatus(201);

    $exchangeRateProvider = collect($exchangeRateProvider)
        ->only([
            'driver',
            'key',
            'active',
        ])
        ->toArray();

    $this->assertDatabaseHas('exchange_rate_providers', $exchangeRateProvider);
});

test('get exchange rate provider', function () {
    $exchangeRateProvider = ExchangeRateProvider::factory()->create();

    getJson("api/v1/exchange-rate-providers/{$exchangeRateProvider->id}")
        ->assertOk();
});

test('update user using a form request', function () {
    $this->assertActionUsesFormRequest(
        ExchangeRateProviderController::class,
        'update',
        ExchangeRateProviderRequest::class
    );
});

test('update exchange rate provider', function () {
    $exchangeRateProvider = ExchangeRateProvider::factory()->create();

    $newExchangeRateProvider = ExchangeRateProvider::factory()->raw();

    putJson("api/v1/exchange-rate-providers/{$exchangeRateProvider->id}", $newExchangeRateProvider)
        ->assertOk();

    $newExchangeRateProvider = collect($newExchangeRateProvider)
        ->only([
            'driver',
            'key',
            'active',
        ])
        ->toArray();

    $this->assertDatabaseHas('exchange_rate_providers', $newExchangeRateProvider);
});

test('delete exchange rate provider', function () {
    $exchangeRateProvider = ExchangeRateProvider::factory()->create([
        'active' => false
    ]);

    deleteJson("api/v1/exchange-rate-providers/{$exchangeRateProvider->id}")
        ->assertOk();

    $this->assertDeleted($exchangeRateProvider);
});
