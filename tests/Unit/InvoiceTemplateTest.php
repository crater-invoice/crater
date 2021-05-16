<?php

use Crater\Models\InvoiceTemplate;
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

test('invoice template has many invoices', function () {
    $invoiceTemplate = InvoiceTemplate::factory()->hasInvoices(5)->create();

    $this->assertCount(5, $invoiceTemplate->invoices);

    $this->assertTrue($invoiceTemplate->invoices()->exists());
});
