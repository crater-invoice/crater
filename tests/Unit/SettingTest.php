<?php

use Illuminate\Support\Facades\Artisan;
use InvoiceShelf\Models\Setting;

use function Pest\Faker\fake;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);
});

test('set setting', function () {
    $key = fake()->name;

    $value = fake()->word;

    Setting::setSetting($key, $value);

    $response = Setting::getSetting($key);

    $this->assertEquals($value, $response);
});
