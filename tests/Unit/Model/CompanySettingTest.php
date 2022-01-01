<?php

use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Illuminate\Support\Facades\Artisan;
use function Pest\Faker\faker;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);
});

test('company setting belongs to company', function () {
    $setting = CompanySetting::factory()->create();

    $this->assertTrue($setting->company()->exists());
});

test('set settings', function () {
    $key = faker()->name;

    $value = faker()->word;

    $company = Company::factory()->create();

    CompanySetting::setSettings([$key => $value], $company->id);

    $response = CompanySetting::getSetting($key, $company->id);

    $this->assertEquals($value, $response);
});

test('get settings', function () {
    $key = faker()->name;

    $value = faker()->word;

    $company = Company::factory()->create();

    CompanySetting::setSettings([$key => $value], $company->id);

    $response = CompanySetting::getSettings([$key], $company->id);

    $this->assertEquals([$key => $value], $response->toArray());
});
