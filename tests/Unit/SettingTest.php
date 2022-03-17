<?php

use Crater\Models\Setting;
use Illuminate\Support\Facades\Artisan;
use function Pest\Faker\faker;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);
});

test('set setting', function () {
    $key = faker()->name;

    $value = faker()->word;

    Setting::setSetting($key, $value);

    $response = Setting::getSetting($key);

    $this->assertEquals($value, $response);
});
