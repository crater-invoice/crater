<?php

use Crater\Models\Unit;
use Crater\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'UnitSeeder', '--force' => true]);

    $user = User::where('role', 'super admin')->first();
    $this->withHeaders([
        'company' => $user->company_id,
    ]);
    Sanctum::actingAs(
        $user,
        ['*']
    );
});

test('unit has many items', function () {
    $unit = Unit::factory()->hasItems(5)->create();

    $this->assertCount(5, $unit->items);
    $this->assertTrue($unit->items()->exists());
});

test('unit belongs to company', function () {
    $unit = Unit::factory()->create();

    $this->assertTrue($unit->company()->exists());
});
