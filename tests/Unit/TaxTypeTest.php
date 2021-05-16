<?php

use Crater\Models\TaxType;
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

test('tax type has many taxes', function () {
    $taxtype = TaxType::factory()->hasTaxes(4)->create();

    $this->assertCount(4, $taxtype->taxes);
    $this->assertTrue($taxtype->taxes()->exists());
});
