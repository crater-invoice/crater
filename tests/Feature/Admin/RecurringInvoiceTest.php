<?php

use Carbon\Carbon;
use Crater\Http\Controllers\V1\Admin\RecurringInvoice\RecurringInvoiceController;
use Crater\Http\Requests\RecurringInvoiceRequest;
use Crater\Models\InvoiceItem;
use Crater\Models\RecurringInvoice;
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
        'company' => $user->companies()->first()->id,
    ]);
    Sanctum::actingAs(
        $user,
        ['*']
    );
});

test('get recurring invoices', function () {
    RecurringInvoice::factory()->create();

    getJson('api/v1/recurring-invoices?page=1')
        ->assertOk();
});

test('store user using a form request', function () {
    $this->assertActionUsesFormRequest(
        RecurringInvoiceController::class,
        'store',
        RecurringInvoiceRequest::class
    );
});

test('store recurring invoice', function () {
    $recurringInvoice = RecurringInvoice::factory()->raw();
    $recurringInvoice['items'] = [
        InvoiceItem::factory()->raw()
    ];

    postJson('api/v1/recurring-invoices', $recurringInvoice)
        ->assertStatus(201);

    $recurringInvoice = collect($recurringInvoice)
        ->only([
            'frequency',
        ])
        ->toArray();

    $this->assertDatabaseHas('recurring_invoices', $recurringInvoice);
});

test('get recurring invoice', function () {
    $recurringInvoice = RecurringInvoice::factory()->create();

    getJson("api/v1/recurring-invoices/{$recurringInvoice->id}")
        ->assertOk();
});

test('update user using a form request', function () {
    $this->assertActionUsesFormRequest(
        RecurringInvoiceController::class,
        'update',
        RecurringInvoiceRequest::class
    );
});

test('update recurring invoice', function () {
    $recurringInvoice = RecurringInvoice::factory()->create();
    $recurringInvoice['items'] = [
        InvoiceItem::factory()->raw()
    ];

    $new_recurringInvoice = RecurringInvoice::factory()->raw();
    $new_recurringInvoice['items'] = [
        InvoiceItem::factory()->raw()
    ];

    putJson("api/v1/recurring-invoices/{$recurringInvoice->id}", $new_recurringInvoice)
        ->assertOk();

    $new_recurringInvoice = collect($new_recurringInvoice)
        ->only([
            'frequency',
        ])
        ->toArray();

    $this->assertDatabaseHas('recurring_invoices', $new_recurringInvoice);
});

test('delete multiple recurring invoice', function () {
    $recurringInvoices = RecurringInvoice::factory()->count(3)->create();

    $data = [
        'ids' => $recurringInvoices->pluck('id'),
    ];

    postJson('api/v1/recurring-invoices/delete', $data)
        ->assertOk()
        ->assertJson([
            'success' => true,
        ]);

    foreach ($recurringInvoices as $recurringInvoice) {
        $this->assertDeleted($recurringInvoice);
    }
});

test('calculate frequency for recurring invoice', function () {
    $data = [
        'frequency' => '* * 2 * *',
        'starts_at' => Carbon::now()->format('Y-m-d')
    ];

    $queryString = http_build_query($data, '', '&');

    getJson("api/v1/recurring-invoice-frequency?".$queryString)
        ->assertOk();
});
