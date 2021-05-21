<?php

use Crater\Models\Address;
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

test('an address belongs to user', function () {
    $address = Address::factory()->forUser()->create();

    $this->assertTrue($address->user->exists());
});

test('an address belongs to country', function () {
    $address = Address::factory()->create();

    $this->assertTrue($address->country->exists());
});
