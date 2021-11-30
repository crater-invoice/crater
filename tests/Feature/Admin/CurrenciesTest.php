<?php

use Crater\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\getJson;

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

test('get all used currencies', function () {
    getJson("/api/v1/currencies/used")
        ->assertOk();
});

test('get supported currencies of currency freak', function () {
    $driver = [
        'driver' => "currency_freak",
        'key' => "9ab5bc6424604778ad61103b628518f8"
    ];

    $queryString = http_build_query($driver, '', '&');

    getJson("/api/v1/supported-currencies?".$queryString)
        ->assertOk();
});

test('get supported currencies of currency layer', function () {
    $driver = [
        'driver' => "currency_layer",
        'key' => "2bb7a25e6f24f42a66fde1f57b5210fd"
    ];

    $queryString = http_build_query($driver, '', '&');

    getJson("/api/v1/supported-currencies?".$queryString)
        ->assertOk();
});

test('get supported currencies of open exchange rate', function () {
    $driver = [
        'driver' => "open_exchange_rate",
        'key' => "c5f404d414d245209923cd4f2d4c3875"
    ];

    $queryString = http_build_query($driver, '', '&');

    getJson("/api/v1/supported-currencies?".$queryString)
        ->assertOk();
});

test('get supported currencies of currency converter', function () {
    $driver = [
        'driver' => "currency_converter",
        'key' => "0a1cef4d5f6fd01cc87a",
        'type' => 'FREE'
    ];

    $queryString = http_build_query($driver, '', '&');

    getJson("/api/v1/supported-currencies?".$queryString)
        ->assertOk();
});
