<?php

use Crater\Models\RecurringInvoice;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);
});

test('recurring invoice has many invoices', function () {
    $recurringInvoice = RecurringInvoice::factory()->hasInvoices(5)->create();

    $this->assertCount(5, $recurringInvoice->invoices);

    $this->assertTrue($recurringInvoice->invoices()->exists());
});

test('recurring invoice has many invoice items', function () {
    $recurringInvoice = RecurringInvoice::factory()->hasItems(5)->create();

    $this->assertCount(5, $recurringInvoice->items);

    $this->assertTrue($recurringInvoice->items()->exists());
});

test('recurring invoice has many taxes', function () {
    $recurringInvoice = RecurringInvoice::factory()->hasTaxes(5)->create();

    $this->assertCount(5, $recurringInvoice->taxes);

    $this->assertTrue($recurringInvoice->taxes()->exists());
});

test('recurring invoice belongs to customer', function () {
    $recurringInvoice = RecurringInvoice::factory()->forCustomer()->create();

    $this->assertTrue($recurringInvoice->customer()->exists());
});
