<?php

use Crater\Http\Controllers\V1\Invoice\InvoicesController;
use Crater\Http\Requests\InvoicesRequest;
use Crater\Mail\SendInvoiceMail;
use Crater\Models\Invoice;
use Crater\Models\InvoiceItem;
use Crater\Models\Tax;
use Crater\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);

    $user = User::find(1);
    $this->withHeaders([
        'company' => $user->company_id,
    ]);
    Sanctum::actingAs(
        $user,
        ['*']
    );
});

test('testGetInvoices', function () {
    $response = getJson('api/v1/invoices?page=1&type=OVERDUE&limit=20');

    $response->assertOk();
});

test('create invoice', function () {
    $invoice = Invoice::factory()
        ->raw([
            'taxes' => [Tax::factory()->raw()],
            'items' => [InvoiceItem::factory()->raw()],
        ]);

    $response = postJson('api/v1/invoices', $invoice);

    $response->assertOk();

    $this->assertDatabaseHas('invoices', [
        'template_name' => $invoice['template_name'],
        'invoice_number' => $invoice['invoice_number'],
        'sub_total' => $invoice['sub_total'],
        'discount' => $invoice['discount'],
        'user_id' => $invoice['user_id'],
        'total' => $invoice['total'],
        'tax' => $invoice['tax'],
    ]);

    $this->assertDatabaseHas('invoice_items', $invoice['items'][0]);

    $this->assertDatabaseHas('taxes', $invoice['taxes'][0]);
});

test('create invoice as sent', function () {
    $invoice = Invoice::factory()
        ->raw([
            'taxes' => [Tax::factory()->raw()],
            'items' => [InvoiceItem::factory()->raw()],
        ]);

    $response = postJson('api/v1/invoices', $invoice);

    $response->assertOk();

    $this->assertDatabaseHas('invoices', [
        'invoice_number' => $invoice['invoice_number'],
        'sub_total' => $invoice['sub_total'],
        'total' => $invoice['total'],
        'tax' => $invoice['tax'],
        'discount' => $invoice['discount'],
        'user_id' => $invoice['user_id'],
        'template_name' => $invoice['template_name'],
    ]);

    $this->assertDatabaseHas('invoice_items', $invoice['items'][0]);

    $this->assertDatabaseHas('taxes', $invoice['taxes'][0]);
});

test('store validates using a form request', function () {
    $this->assertActionUsesFormRequest(
        InvoicesController::class,
        'store',
        InvoicesRequest::class
    );
});

test('update invoice', function () {
    $invoice = Invoice::factory()->create([
        'invoice_date' => '1988-07-18',
        'due_date' => '1988-08-18',
    ]);

    $invoice2 = Invoice::factory()
        ->raw([
            'taxes' => [Tax::factory()->raw()],
            'items' => [InvoiceItem::factory()->raw()],
        ]);

    putJson('api/v1/invoices/'.$invoice->id, $invoice2)->assertOk();

    $this->assertDatabaseHas('invoices', [
        'invoice_number' => $invoice2['invoice_number'],
        'sub_total' => $invoice2['sub_total'],
        'total' => $invoice2['total'],
        'tax' => $invoice2['tax'],
        'discount' => $invoice2['discount'],
        'user_id' => $invoice2['user_id'],
        'template_name' => $invoice2['template_name'],
    ]);

    $this->assertDatabaseHas('invoice_items', $invoice2['items'][0]);

    $this->assertDatabaseHas('taxes', $invoice2['taxes'][0]);
});

test('update validates using a form request', function () {
    $this->assertActionUsesFormRequest(
        InvoicesController::class,
        'update',
        InvoicesRequest::class
    );
});

test('send invoice to customer', function () {
    Mail::fake();

    $invoices = Invoice::factory()->create([
        'invoice_date' => '1988-07-18',
        'due_date' => '1988-08-18',
    ]);

    $data = [
        'from' => 'john@example.com',
        'to' => 'doe@example.com',
        'subject' => 'email subject',
        'body' => 'email body',
    ];

    $response = postJson('api/v1/invoices/'.$invoices->id.'/send', $data);

    $response
        ->assertOk()
        ->assertJson([
            'success' => true,
        ]);

    $invoice2 = Invoice::find($invoices->id);

    $this->assertEquals($invoice2->status, Invoice::STATUS_SENT);
    Mail::assertSent(SendInvoiceMail::class);
});

test('invoice mark as paid', function () {
    $invoice = Invoice::factory()->create([
        'invoice_date' => '1988-07-18',
        'due_date' => '1988-08-18',
    ]);

    $data = [
        'status' => Invoice::STATUS_COMPLETED,
    ];

    $response = postJson('api/v1/invoices/'.$invoice->id.'/status', $data);

    $response
        ->assertOk()
        ->assertJson([
            'success' => true,
        ]);

    $this->assertEquals(Invoice::find($invoice->id)->paid_status, Invoice::STATUS_PAID);
});

test('invoice mark as sent', function () {
    $invoice = Invoice::factory()->create([
        'invoice_date' => '1988-07-18',
        'due_date' => '1988-08-18',
    ]);

    $data = [
        'status' => Invoice::STATUS_SENT,
    ];

    $response = postJson('api/v1/invoices/'.$invoice->id.'/status', $data);

    $response
        ->assertOk()
        ->assertJson([
            'success' => true,
        ]);

    $this->assertEquals(Invoice::find($invoice->id)->status, Invoice::STATUS_SENT);
});

test('search invoices', function () {
    $filters = [
        'page' => 1,
        'limit' => 15,
        'search' => 'doe',
        'status' => Invoice::STATUS_DRAFT,
        'from_date' => '2019-01-20',
        'to_date' => '2019-01-27',
        'invoice_number' => '000012',
    ];

    $queryString = http_build_query($filters, '', '&');

    $response = getJson('api/v1/invoices?'.$queryString);

    $response->assertOk();
});

test('delete multiple invoices', function () {
    $invoices = Invoice::factory()->count(3)->create([
        'invoice_date' => '1988-07-18',
        'due_date' => '1988-08-18',
    ]);

    $ids = $invoices->pluck('id');

    $data = [
        'ids' => $ids,
    ];

    postJson('api/v1/invoices/delete', $data)
        ->assertOk()
        ->assertJson([
            'success' => true,
        ]);

    foreach ($invoices as $invoice) {
        $this->assertDeleted($invoice);
    }
});

test('clone invoice', function () {
    $invoices = Invoice::factory()->create([
        'invoice_date' => '1988-07-18',
        'due_date' => '1988-08-18',
    ]);

    $response = postJson("api/v1/invoices/{$invoices->id}/clone");

    $response
        ->assertOk()
        ->assertJson([
            'success' => true,
        ]);
});

test('create invoice with negative tax', function () {
    $invoice = Invoice::factory()
        ->raw([
            'taxes' => [Tax::factory()->raw([
                'percent' => -9.99
            ])],
            'items' => [InvoiceItem::factory()->raw()],
        ]);

    $response = postJson('api/v1/invoices', $invoice);

    $response->assertOk();

    $this->assertDatabaseHas('invoices', [
        'invoice_number' => $invoice['invoice_number'],
        'sub_total' => $invoice['sub_total'],
        'total' => $invoice['total'],
        'tax' => $invoice['tax'],
        'discount' => $invoice['discount'],
        'user_id' => $invoice['user_id'],
    ]);

    $this->assertDatabaseHas('invoice_items', $invoice['items'][0]);

    $this->assertDatabaseHas('taxes', $invoice['taxes'][0]);
});
