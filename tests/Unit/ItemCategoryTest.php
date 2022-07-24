<?php

use Crater\Models\ExpenseCategory;
use Crater\Models\ItemCategory;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);
});

test('item category has many items', function () {
    $category = ItemCategory::factory()->hasItems(5)->create();

    $this->assertCount(5, $category->items);
    $this->assertTrue($category->items()->exists());
});
