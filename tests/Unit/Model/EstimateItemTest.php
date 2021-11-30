<?php

use Crater\Models\Estimate;
use Crater\Models\EstimateItem;
use Crater\Models\Item;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);
});

test('estimate item belongs to estimate', function () {
    $estimateItem = EstimateItem::factory()->forEstimate()->create();

    $this->assertTrue($estimateItem->estimate()->exists());
});

test('estimate item belongs to item', function () {
    $estimateItem = EstimateItem::factory()->create([
        'item_id' => Item::factory(),
        'estimate_id' => Estimate::factory(),
    ]);

    $this->assertTrue($estimateItem->item()->exists());
});


test('estimate item has many taxes', function () {
    $estimateItem = EstimateItem::factory()->hasTaxes(5)->create([
        'estimate_id' => Estimate::factory(),
    ]);

    $this->assertCount(5, $estimateItem->taxes);

    $this->assertTrue($estimateItem->taxes()->exists());
});
