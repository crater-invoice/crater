<?php

use Crater\Models\TaxType;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);
});

test('tax type has many taxes', function () {
    $taxtype = TaxType::factory()->hasTaxes(4)->create();

    $this->assertCount(4, $taxtype->taxes);
    $this->assertTrue($taxtype->taxes()->exists());
});
