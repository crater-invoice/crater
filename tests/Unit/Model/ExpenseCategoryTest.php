<?php

use Crater\Models\ExpenseCategory;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);
});

test('expense category has many expenses', function () {
    $category = ExpenseCategory::factory()->hasExpenses(5)->create();

    $this->assertCount(5, $category->expenses);
    $this->assertTrue($category->expenses()->exists());
});
