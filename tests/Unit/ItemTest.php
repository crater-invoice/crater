<?php

use Crater\Models\Estimate;
use Crater\Models\EstimateItem;
use Crater\Models\Invoice;
use Crater\Models\InvoiceItem;
use Crater\Models\Item;
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

test('an item belongs to unit', function () {
    $item = Item::factory()->forUnit()->create();

    $this->assertTrue($item->unit()->exists());
});

test('an item has many taxes', function () {
    $item = Item::factory()->hasTaxes(5)->create();

    $this->assertCount(5, $item->taxes);
    $this->assertTrue($item->taxes()->exists());
});

test('an item has many invoice items', function () {
    $item = Item::factory()->has(InvoiceItem::factory()->count(5)->state([
        'invoice_id' => Invoice::factory(),
    ]))->create();

    $this->assertCount(5, $item->invoiceItems);

    $this->assertTrue($item->invoiceItems()->exists());
});

test('an item has many estimate items', function () {
    $item = Item::factory()->has(EstimateItem::factory()
        ->count(5)
        ->state([
            'estimate_id' => Estimate::factory(),
        ]))
        ->create();

    $this->assertCount(5, $item->estimateItems);

    $this->assertTrue($item->estimateItems()->exists());
});
