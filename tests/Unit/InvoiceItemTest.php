<?php

use Crater\Models\Invoice;
use Crater\Models\InvoiceItem;
use Crater\Models\Item;
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

test('invoice item belongs to invoice', function () {
    $invoiceItem = InvoiceItem::factory()->forInvoice()->create();

    $this->assertTrue($invoiceItem->invoice()->exists());
});

test('invoice item belongs to item', function () {
    $invoiceItem = InvoiceItem::factory()->create([
        'item_id' => Item::factory(),
        'invoice_id' => Invoice::factory(),
    ]);

    $this->assertTrue($invoiceItem->item()->exists());
});


test('invoice item has many taxes', function () {
    $invoiceItem = InvoiceItem::factory()->hasTaxes(5)->create([
        'invoice_id' => Invoice::factory(),
    ]);

    $this->assertCount(5, $invoiceItem->taxes);

    $this->assertTrue($invoiceItem->taxes()->exists());
});
