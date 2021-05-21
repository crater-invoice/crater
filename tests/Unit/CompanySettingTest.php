<?php

use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use function Pest\Faker\faker;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);

    $user = User::find(1);
    $this->withHeaders([
        'company' => $user->company_id,
    ]);
    Sanctum::actingAs(
        $user,
        ['*']
    );
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

    $this->assertEquals([$key => $value], $response);
});
