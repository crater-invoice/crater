<?php

use Crater\Http\Controllers\V1\Payment\PaymentsController;
use Crater\Http\Requests\PaymentRequest;
use Crater\Mail\SendPaymentMail;
use Crater\Models\Invoice;
use Crater\Models\Payment;
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

test('get payments', function () {
    $response = getJson('api/v1/payments?page=1');

    $response->assertOk();
});

test('get payment', function () {
    $payment = Payment::factory()->create();

    $response = getJson("api/v1/payments/{$payment->id}");

    $response->assertStatus(200);
});

test('create payment', function () {
    $invoice = Invoice::factory()->create([
        'due_amount' => 100,
    ]);

    $payment = Payment::factory()->raw([
        'invoice_id' => $invoice->id,
        'payment_number' => "PAY-000001",
    ]);

    $response = postJson('api/v1/payments', $payment);

    $response->assertOk();

    $this->assertDatabaseHas('payments', [
        'payment_number' => $payment['payment_number'],
        'user_id' => $payment['user_id'],
        'amount' => $payment['amount'],
        'company_id' => $payment['company_id'],
    ]);
});

test('store validates using a form request', function () {
    $this->assertActionUsesFormRequest(
        PaymentsController::class,
        'store',
        PaymentRequest::class
    );
});

test('update payment', function () {
    $payment = Payment::factory()->create([
        'payment_date' => '1988-08-18',
    ]);

    $payment2 = Payment::factory()->raw([
        'payment_number' => $payment->payment_number,
    ]);

    $response = putJson("api/v1/payments/{$payment->id}", $payment2);

    $response->assertOk();

    $this->assertDatabaseHas('payments', [
        'id' => $payment->id,
        'payment_number' => $payment2['payment_number'],
        'user_id' => $payment2['user_id'],
        'amount' => $payment2['amount'],
    ]);
});

test('update validates using a form request', function () {
    $this->assertActionUsesFormRequest(
        PaymentsController::class,
        'update',
        PaymentRequest::class
    );
});

test('search payments', function () {
    $filters = [
        'page' => 1,
        'limit' => 15,
        'search' => 'doe',
        'payment_number' => 'PAY-000001',
        'payment_mode' => 'OTHER',
    ];

    $queryString = http_build_query($filters, '', '&');

    $response = getJson('api/v1/payments?'.$queryString);

    $response->assertOk();
});

test('send payment to customer', function () {
    Mail::fake();

    $payment = Payment::factory()->create();

    $data = [
        'subject' => 'test',
        'body' => 'test',
        'from' => 'john@example.com',
        'to' => 'doe@example.com',
    ];

    $response = postJson("api/v1/payments/{$payment->id}/send", $data);

    $response->assertJson([
        'success' => true,
    ]);

    Mail::assertSent(SendPaymentMail::class);
});

test('delete payment', function () {
    $payments = Payment::factory()->count(5)->create();

    $ids = $payments->pluck('id');

    $data = [
        'ids' => $ids,
    ];

    $response = postJson('api/v1/payments/delete', $data);

    $response->assertJson([
        'success' => true,
    ]);
});
