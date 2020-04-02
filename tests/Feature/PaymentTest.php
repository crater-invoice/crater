<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crater\User;
use Crater\Invoice;
use Crater\Payment;
use Laravel\Passport\Passport;
use SettingsSeeder;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->seed(SettingsSeeder::class);
        $user = User::find(1);
        $this->withHeaders([
            'company' => $user->company_id,
        ]);
        Passport::actingAs(
            $user,
            ['*']
        );
    }

    /** @test */
    public function testGetPayments()
    {
        $response = $this->json('GET', 'api/payments?page=1');

        $response->assertOk();
    }

    /** @test */
    public function testGetCreatePaymentData()
    {
        $response = $this->json('GET', 'api/payments/create');

        $response->assertOk();
    }

    /** @test */
    public function testCreatePayment()
    {
        $payment = factory(Payment::class)->raw();

        $response = $this->json('POST', 'api/payments', $payment);

        $payment2 = $response->decodeResponseJson()['payment'];

        $response->assertOk();
        $this->assertEquals($payment['payment_number'], $payment2['payment_number']);
        $this->assertEquals($payment['user_id'], $payment2['user_id']);
        $this->assertEquals($payment['amount'], $payment2['amount']);
    }

    /** @test */
    public function testCreatePaymentRequiresPaymentNumber()
    {
        $payment = factory(Payment::class)->raw([
            'payment_number' => ''
        ]);

        $response = $this->json('POST', 'api/payments', $payment);

        $response->assertStatus(422)->assertJsonValidationErrors(['payment_number']);
    }

    /** @test */
    public function testCreatePaymentRequiresAmount()
    {
        $payment = factory(Payment::class)->raw([
            'amount' => ''
        ]);

        $response = $this->json('POST', 'api/payments', $payment);

        $response->assertStatus(422)->assertJsonValidationErrors(['amount']);
    }

    /** @test */
    public function testCreatePaymentRequiresUser()
    {
        $payment = factory(Payment::class)->raw([
            'user_id' => ''
        ]);

        $response = $this->json('POST', 'api/payments', $payment);

        $response->assertStatus(422)->assertJsonValidationErrors(['user_id']);
    }

    /** @test */
    public function testCreatePaymentRequiresPaymentDate()
    {
        $payment = factory(Payment::class)->raw([
            'payment_date' => ''
        ]);

        $response = $this->json('POST', 'api/payments', $payment);

        $response->assertStatus(422)->assertJsonValidationErrors(['payment_date']);
    }

    /** @test */
    public function testGetEditPaymentData()
    {
        $payment = factory(Payment::class)->create([
            'payment_date' => '1988-07-18'
        ]);

        $response = $this->json('GET', 'api/payments/'.$payment->id.'/edit');

        $response->assertOk();
    }

    /** @test */
    public function testUpdateEstimate()
    {
        $payment = factory(Payment::class)->create([
            'payment_date' => '1988-07-18'
        ]);

        $payment2 = factory(Payment::class)->raw([
            'payment_number' => $payment->payment_number
        ]);

        $response = $this->json('PUT', 'api/payments/'.$payment->id, $payment2);

        $payment3 = $response->decodeResponseJson()['payment'];

        $response->assertOk();
        $this->assertEquals($payment3['payment_number'], $payment2['payment_number']);
        $this->assertEquals($payment3['user_id'], $payment2['user_id']);
        $this->assertEquals($payment3['amount'], $payment2['amount']);
    }

    /** @test */
    public function testUpdatePaymentRequiresPaymentDate()
    {
        $payment = factory(Payment::class)->create([
            'payment_date' => '1988-07-18'
        ]);

        $payment2 = factory(Payment::class)->raw(['payment_date' => '']);

        $response = $this->json('PUT', 'api/payments/'.$payment->id, $payment2);

        $response->assertStatus(422)->assertJsonValidationErrors(['payment_date']);
    }

    /** @test */
    public function testUpdatePaymentRequiresPaymentNumber()
    {
        $payment = factory(Payment::class)->create([
            'payment_date' => '1988-07-18'
        ]);

        $payment2 = factory(Payment::class)->raw(['payment_number' => '']);

        $response = $this->json('PUT', 'api/payments/'.$payment->id, $payment2);

        $response->assertStatus(422)->assertJsonValidationErrors(['payment_number']);
    }

    /** @test */
    public function testUpdatePaymentRequiresAmount()
    {
        $payment = factory(Payment::class)->create([
            'payment_date' => '1988-07-18'
        ]);

        $payment2 = factory(Payment::class)->raw(['amount' => '']);

        $response = $this->json('PUT', 'api/payments/'.$payment->id, $payment2);

        $response->assertStatus(422)->assertJsonValidationErrors(['amount']);
    }

    /** @test */
    public function testUpdatePaymentRequiresUser()
    {
        $payment = factory(Payment::class)->create([
            'payment_date' => '1988-07-18'
        ]);

        $payment2 = factory(Payment::class)->raw(['user_id' => '']);

        $response = $this->json('PUT', 'api/payments/'.$payment->id, $payment2);

        $response->assertStatus(422)->assertJsonValidationErrors(['user_id']);
    }

    /** @test */
    public function testDeletePayment()
    {
        $payment = factory(Payment::class)->create([
            'payment_date' => '1988-07-18'
        ]);

        $response = $this->json('DELETE', 'api/payments/'.$payment->id);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $payment = Payment::find($payment->id);
        $this->assertNull($payment);
    }

    /** @test */
    public function testSearchPayments()
    {
        $filters = [
            'page' => 1,
            'limit' => 15,
            'search' => 'doe',
            'payment_number' => 'PAY-000001',
            'payment_mode' => 'OTHER'
        ];

        $queryString = http_build_query($filters, '', '&');

        $response = $this->json('GET', 'api/payments?'.$queryString);

        $response->assertOk();
    }

    /** @test */
    public function getUnpaidInvoicesOfUser()
    {
        $user = factory(User::class)->create();

        $invoices = factory(Invoice::class, 2)->create([
            'invoice_date' => '1988-07-18',
            'due_date' => '1988-08-18',
            'user_id' => $user->id
        ]);

        $response = $this->json('GET', 'api/invoices/unpaid/'.$user->id);

        $response->assertOk();
    }

    /** @test */
    public function testDeleteMultiplePayments()
    {
        $payments = factory(Payment::class, 3)->create([
            'payment_date' => '1988-07-18'
        ]);

        $ids = $payments->pluck('id');

        $data = [
            'id' => $ids,
            'type' => 'payment'
        ];

        $response = $this->json('POST', 'api/payments/delete', $data);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true
            ]);
    }
}
