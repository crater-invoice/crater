<?php

use Crater\Models\PaymentMethod;
use Crater\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'UnitSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'PaymentMethodSeeder', '--force' => true]);

    $user = User::where('role', 'super admin')->first();
    $this->withHeaders([
        'company' => $user->company_id,
    ]);
    Sanctum::actingAs(
        $user,
        ['*']
    );
});

test('payment method has many payment', function () {
    $method = PaymentMethod::factory()->hasPayments(5)->create();

    $this->assertCount(5, $method->payments);

    $this->assertTrue($method->payments()->exists());
});

test('payment method belongs to company', function () {
    $method = PaymentMethod::factory()->create();

    $this->assertTrue($method->company()->exists());
});
