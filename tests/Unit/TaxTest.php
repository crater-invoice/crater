<?php

use Crater\Models\Estimate;
use Crater\Models\EstimateItem;
use Crater\Models\Invoice;
use Crater\Models\InvoiceItem;
use Crater\Models\Tax;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);
});

test('tax belongs to tax type', function () {
    $tax = Tax::factory()->create();

    $this->assertTrue($tax->taxType->exists());
});

test('tax belongs to invoice', function () {
    $tax = Tax::factory()->forInvoice()->create();

    $this->assertTrue($tax->invoice()->exists());
});

test('tax belongs to recurring invoice', function () {
    $tax = Tax::factory()->forRecurringInvoice()->create();

    $this->assertTrue($tax->recurringInvoice()->exists());
});

test('tax belongs to estimate', function () {
    $tax = Tax::factory()->forEstimate()->create();

    $this->assertTrue($tax->estimate()->exists());
});

test('tax belongs to invoice item', function () {
    $tax = Tax::factory()->for(InvoiceItem::factory()->state([
        'invoice_id' => Invoice::factory(),
    ]))->create();

    $this->assertTrue($tax->invoiceItem()->exists());
});

test('tax belongs to estimate item', function () {
    $tax = Tax::factory()->for(EstimateItem::factory()->state([
        'estimate_id' => Estimate::factory(),
    ]))->create();

    $this->assertTrue($tax->estimateItem()->exists());
});

test('tax belongs to item', function () {
    $tax = Tax::factory()->forItem()->create();

    $this->assertTrue($tax->item()->exists());
});
