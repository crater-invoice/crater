<?php

use Crater\Models\Company;
use Crater\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;

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

test('company has one user', function () {
    $company = Company::factory()->hasUser()->create();

    $this->assertTrue($company->user()->exists());
});

test('company has many company setings', function () {
    $company = Company::factory()->hasSettings(5)->create();

    $this->assertCount(5, $company->settings);

    $this->assertTrue($company->settings()->exists());
});
