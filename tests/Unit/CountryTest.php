<?php

use Crater\Models\Address;
use Crater\Models\Country;
use Crater\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);

    $user = User::where('role', 'super admin')->first();
    $this->withHeaders([
        'company' => $user->company_id,
    ]);
    Sanctum::actingAs(
        $user,
        ['*']
    );
});

test('country has many addresses', function () {
    $country = Country::find(1);

    $address = Address::factory()->count(5)->create([
        'country_id' => $country->id,
    ]);

    $this->assertTrue($country->address()->exists());
});
