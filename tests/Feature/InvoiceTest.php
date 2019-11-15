<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crater\Tax;
use Crater\User;
use Crater\Invoice;
use Crater\CompanySetting;
use Crater\InvoiceItem;
use Laravel\Passport\Passport;
use SettingsSeeder;

class InvoiceTest extends TestCase
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
            'company' => 1,
        ]);
        Passport::actingAs(
            $user,
            ['*']
        );
    }


    /** @test */
    public function testGetInvoices()
    {
        $response = $this->json('GET', 'api/invoices?page=1&type=OVERDUE&limit=20');

        $response->assertOk();
    }

    /** @test */
    public function testGetCreateInvoiceData()
    {
        $response = $this->json('GET', 'api/invoices/create');

        $response->assertOk();
    }

    /** @test */
    public function testCreateInvoice()
    {
        $invoice = factory(Invoice::class)->raw([
            'items' => [
                factory(InvoiceItem::class)->raw([
                    'taxes' => [
                        factory(Tax::class)->raw()
                    ]
                ])
            ],
            'taxes' => [
                factory(Tax::class)->raw()
            ]
        ]);

        $response = $this->json('POST', 'api/invoices', $invoice);

        $invoice2 = $response->decodeResponseJson()['invoice'];

        $response->assertOk();
        $this->assertEquals($invoice['invoice_number'], $invoice2['invoice_number']);
        $this->assertEquals($invoice['sub_total'], $invoice2['sub_total']);
        $this->assertEquals($invoice['total'], $invoice2['total']);
        $this->assertEquals($invoice['tax'], $invoice2['tax']);
        $this->assertEquals($invoice['discount'], $invoice2['discount']);
        $this->assertEquals($invoice['discount_type'], $invoice2['discount_type']);
        $this->assertEquals($invoice['discount_val'], $invoice2['discount_val']);
        $this->assertEquals($invoice['notes'], $invoice2['notes']);
        $this->assertEquals($invoice['user_id'], $invoice2['user_id']);
        $this->assertEquals($invoice['invoice_template_id'], $invoice2['invoice_template_id']);
        $this->assertEquals($invoice['items'][0]['name'], $invoice2['items'][0]['name']);
        $this->assertEquals($invoice['items'][0]['description'], $invoice2['items'][0]['description']);
        $this->assertEquals($invoice['items'][0]['price'], $invoice2['items'][0]['price']);
        $this->assertEquals($invoice['items'][0]['quantity'], $invoice2['items'][0]['quantity']);
        $this->assertEquals($invoice['items'][0]['discount_type'], $invoice2['items'][0]['discount_type']);
        $this->assertEquals($invoice['items'][0]['discount'], $invoice2['items'][0]['discount']);
        $this->assertEquals($invoice['items'][0]['total'], $invoice2['items'][0]['total']);
        $this->assertEquals($invoice['items'][0]['item_id'], $invoice2['items'][0]['item_id']);
        $this->assertEquals($invoice['taxes'][0]['tax_type_id'], $invoice2['taxes'][0]['tax_type_id']);
        $this->assertEquals($invoice['taxes'][0]['percent'], $invoice2['taxes'][0]['percent']);
        $this->assertEquals($invoice['taxes'][0]['amount'], $invoice2['taxes'][0]['amount']);
        $this->assertEquals($invoice2['id'], $invoice2['taxes'][0]['invoice_id']);
    }

    /** @test */
    public function testCreateInvoiceAsSent()
    {
        $invoice = factory(Invoice::class)->raw([
            // 'invoiceSend' => true,
            'items' => [
                factory(InvoiceItem::class)->raw([
                    'taxes' => [
                        factory(Tax::class)->raw()
                    ]
                ])
            ],
            'taxes' => [
                factory(Tax::class)->raw()
            ]
        ]);

        $response = $this->json('POST', 'api/invoices', $invoice);

        $invoice2 = $response->decodeResponseJson()['invoice'];

        $response->assertOk();
        $this->assertEquals($invoice['invoice_number'], $invoice2['invoice_number']);
        $this->assertEquals($invoice['sub_total'], $invoice2['sub_total']);
        $this->assertEquals($invoice['total'], $invoice2['total']);
        $this->assertEquals($invoice['tax'], $invoice2['tax']);
        $this->assertEquals($invoice['discount'], $invoice2['discount']);
        $this->assertEquals($invoice['discount_type'], $invoice2['discount_type']);
        $this->assertEquals($invoice['discount_val'], $invoice2['discount_val']);
        $this->assertEquals($invoice['notes'], $invoice2['notes']);
        $this->assertEquals($invoice['user_id'], $invoice2['user_id']);
        $this->assertEquals($invoice['invoice_template_id'], $invoice2['invoice_template_id']);
        $this->assertEquals($invoice['items'][0]['name'], $invoice2['items'][0]['name']);
        $this->assertEquals($invoice['items'][0]['description'], $invoice2['items'][0]['description']);
        $this->assertEquals($invoice['items'][0]['price'], $invoice2['items'][0]['price']);
        $this->assertEquals($invoice['items'][0]['quantity'], $invoice2['items'][0]['quantity']);
        $this->assertEquals($invoice['items'][0]['discount_type'], $invoice2['items'][0]['discount_type']);
        $this->assertEquals($invoice['items'][0]['discount'], $invoice2['items'][0]['discount']);
        $this->assertEquals($invoice['items'][0]['total'], $invoice2['items'][0]['total']);
        $this->assertEquals($invoice['items'][0]['item_id'], $invoice2['items'][0]['item_id']);
        $this->assertEquals($invoice['taxes'][0]['tax_type_id'], $invoice2['taxes'][0]['tax_type_id']);
        $this->assertEquals($invoice['taxes'][0]['percent'], $invoice2['taxes'][0]['percent']);
        $this->assertEquals($invoice['taxes'][0]['amount'], $invoice2['taxes'][0]['amount']);
        $this->assertEquals($invoice2['id'], $invoice2['taxes'][0]['invoice_id']);
    }

    /** @test */
    public function testCreateInvoiceRequiresInvoiceDate()
    {
        $invoice = factory(Invoice::class)->raw([
            'invoice_date' => '',
            'items' => [
                factory(InvoiceItem::class)->raw()
            ]
        ]);

        $response = $this->json('POST', 'api/invoices', $invoice);

        $response->assertStatus(422)->assertJsonValidationErrors(['invoice_date']);
    }

    /** @test */
    public function testCreateInvoiceExpiryDateRequired()
    {
        $invoice = factory(Invoice::class)->raw([
            'due_date' => '',
            'items' => [
                factory(InvoiceItem::class)->raw()
            ]
        ]);

        $response = $this->json('POST', 'api/invoices', $invoice);

        $response->assertStatus(422)->assertJsonValidationErrors(['due_date']);
    }

    /** @test */
    public function testCreateInvoiceRequiresInvoiceNumber()
    {
        $invoice = factory(Invoice::class)->raw([
            'invoice_number' => '',
            'items' => [
                factory(InvoiceItem::class)->raw()
            ]
        ]);

        $response = $this->json('POST', 'api/invoices', $invoice);

        $response->assertStatus(422)->assertJsonValidationErrors(['invoice_number']);
    }


    /** @test */
    public function testCreateInvoiceRequiresUser()
    {
        $invoice = factory(Invoice::class)->raw([
            'user_id' => '',
            'items' => [
                factory(InvoiceItem::class)->raw()
            ]
        ]);

        $response = $this->json('POST', 'api/invoices', $invoice);

        $response->assertStatus(422)->assertJsonValidationErrors(['user_id']);
    }

    /** @test */
    public function testCreateInvoiceRequiresDiscount()
    {
        $invoice = factory(Invoice::class)->raw([
            'discount' => '',
            'items' => [
                factory(InvoiceItem::class)->raw()
            ]
        ]);

        $response = $this->json('POST', 'api/invoices', $invoice);

        $response->assertStatus(422)->assertJsonValidationErrors(['discount']);
    }

    /** @test */
    public function testCreateInvoiceRequiresTemplate()
    {
        $invoice = factory(Invoice::class)->raw([
            'invoice_template_id' => '',
            'items' => [
                factory(InvoiceItem::class)->raw()
            ]
        ]);

        $response = $this->json('POST', 'api/invoices', $invoice);

        $response->assertStatus(422)->assertJsonValidationErrors(['invoice_template_id']);
    }

    /** @test */
    public function testCreateInvoiceRequiresItems()
    {
        $invoice = factory(Invoice::class)->raw(['items' => []]);

        $response = $this->json('POST', 'api/invoices', $invoice);

        $response->assertStatus(422)->assertJsonValidationErrors(['items']);
    }

    /** @test */
    public function testCreateInvoiceRequiresItemsName()
    {
        $invoice = factory(Invoice::class)->raw([
            'items' => [factory(InvoiceItem::class)->raw(['name' => ''])]
        ]);

        $response = $this->json('POST', 'api/invoices', $invoice);

        $response->assertStatus(422)->assertJsonValidationErrors(['items.0.name']);
    }

    /** @test */
    public function testCreateInvoiceRequiresItemsQuantity()
    {
        $invoice = factory(Invoice::class)->raw([
            'items' => [factory(InvoiceItem::class)->raw(['quantity' => null])]
        ]);

        $response = $this->json('POST', 'api/invoices', $invoice);

        $response->assertStatus(422)->assertJsonValidationErrors(['items.0.quantity']);
    }

    /** @test */
    public function testCreateInvoiceRequiresItemsPrice()
    {
        $invoice = factory(Invoice::class)->raw([
            'items' => [factory(InvoiceItem::class)->raw(['price' => null])]
        ]);

        $response = $this->json('POST', 'api/invoices', $invoice);

        $response->assertStatus(422)->assertJsonValidationErrors(['items.0.price']);
    }

    /** @test */
    public function testGetEditInvoiceData()
    {
        $invoice = factory(Invoice::class)->create([
            'invoice_date' => '1988-07-18',
            'due_date' => '1988-08-18',
        ]);

        $response = $this->json('GET', 'api/invoices/'.$invoice->id.'/edit');

        $response->assertOk();
    }

    /** @test */
    public function testUpdateInvoice()
    {
        $invoice = factory(Invoice::class)->create([
            'invoice_date' => '1988-07-18',
            'due_date' => '1988-08-18',
        ]);

        $invoice2 = factory(Invoice::class)->raw([
            'items' => [
                factory(InvoiceItem::class)->raw([
                    'taxes' => [
                        factory(Tax::class)->raw()
                    ]
                ])
            ],
            'taxes' => [
                factory(Tax::class)->raw(['tax_type_id' => $invoice->taxes[0]->tax_type_id])
            ]
        ]);

        $response = $this->json('PUT', 'api/invoices/'.$invoice->id, $invoice2);

        $invoice3 = $response->decodeResponseJson()['invoice'];

        $response->assertOk();
        $this->assertEquals($invoice3['invoice_number'], $invoice2['invoice_number']);
        $this->assertEquals($invoice3['sub_total'], $invoice2['sub_total']);
        $this->assertEquals($invoice3['total'], $invoice2['total']);
        $this->assertEquals($invoice3['tax'], $invoice2['tax']);
        $this->assertEquals($invoice3['discount'], $invoice2['discount']);
        $this->assertEquals($invoice3['notes'], $invoice2['notes']);
        $this->assertEquals($invoice3['user_id'], $invoice2['user_id']);
        $this->assertEquals($invoice3['invoice_template_id'], $invoice2['invoice_template_id']);
        $this->assertEquals($invoice3['items'][0]['name'], $invoice2['items'][0]['name']);
        $this->assertEquals($invoice3['items'][0]['description'], $invoice2['items'][0]['description']);
        $this->assertEquals($invoice3['items'][0]['price'], $invoice2['items'][0]['price']);
        $this->assertEquals($invoice3['items'][0]['quantity'], $invoice2['items'][0]['quantity']);
        $this->assertEquals($invoice3['items'][0]['discount_type'], $invoice2['items'][0]['discount_type']);
        $this->assertEquals($invoice3['items'][0]['discount'], $invoice2['items'][0]['discount']);
        $this->assertEquals($invoice3['items'][0]['total'], $invoice2['items'][0]['total']);
        $this->assertEquals($invoice3['items'][0]['item_id'], $invoice2['items'][0]['item_id']);
        $this->assertEquals($invoice3['taxes'][0]['tax_type_id'], $invoice2['taxes'][0]['tax_type_id']);
        $this->assertEquals($invoice3['taxes'][0]['percent'], $invoice2['taxes'][0]['percent']);
        $this->assertEquals($invoice3['taxes'][0]['amount'], $invoice2['taxes'][0]['amount']);
        $this->assertEquals($invoice->id, $invoice3['taxes'][0]['invoice_id']);
    }

    /** @test */
    public function testUpdateInvoiceRequiresInvoiceDate()
    {
        $invoice = factory(Invoice::class)->create([
            'invoice_date' => '1988-07-18',
            'due_date' => '1988-08-18',
        ]);

        $invoice2 = factory(Invoice::class)->raw([
            'invoice_date' => '',
            'items' => [
                factory(InvoiceItem::class)->raw()
            ]
        ]);

        $response = $this->json('PUT', 'api/invoices/'.$invoice->id, $invoice2);

        $response->assertStatus(422)->assertJsonValidationErrors(['invoice_date']);
    }

    /** @test */
    public function testUpdateInvoiceExpiryDateRequired()
    {
        $invoice = factory(Invoice::class)->create([
            'invoice_date' => '1988-07-18',
            'due_date' => '1988-08-18',
        ]);

        $invoice2 = factory(Invoice::class)->raw([
            'due_date' => '',
            'items' => [
                factory(InvoiceItem::class)->raw()
            ]
        ]);

        $response = $this->json('PUT', 'api/invoices/'.$invoice->id, $invoice2);

        $response->assertStatus(422)->assertJsonValidationErrors(['due_date']);
    }

    /** @test */
    public function testUpdateInvoiceRequiresInvoiceNumber()
    {
        $invoice = factory(Invoice::class)->create([
            'invoice_date' => '1988-07-18',
            'due_date' => '1988-08-18',
        ]);

        $invoice2 = factory(Invoice::class)->raw([
            'invoice_number' => '',
            'items' => [
                factory(InvoiceItem::class)->raw()
            ]
        ]);

        $response = $this->json('PUT', 'api/invoices/'.$invoice->id, $invoice2);

        $response->assertStatus(422)->assertJsonValidationErrors(['invoice_number']);
    }


    /** @test */
    public function testUpdateInvoiceRequiresUser()
    {
        $invoice = factory(Invoice::class)->create([
            'invoice_date' => '1988-07-18',
            'due_date' => '1988-08-18',
        ]);

        $invoice2 = factory(Invoice::class)->raw([
            'user_id' => '',
            'items' => [
                factory(InvoiceItem::class)->raw()
            ]
        ]);

        $response = $this->json('PUT', 'api/invoices/'.$invoice->id, $invoice2);

        $response->assertStatus(422)->assertJsonValidationErrors(['user_id']);
    }

    /** @test */
    public function testUpdateInvoiceRequiresDiscount()
    {
        $invoice = factory(Invoice::class)->create([
            'invoice_date' => '1988-07-18',
            'due_date' => '1988-08-18',
        ]);

        $invoice2 = factory(Invoice::class)->raw([
            'discount' => '',
            'items' => [
                factory(InvoiceItem::class)->raw()
            ]
        ]);

        $response = $this->json('PUT', 'api/invoices/'.$invoice->id, $invoice2);

        $response->assertStatus(422)->assertJsonValidationErrors(['discount']);
    }

    /** @test */
    public function testUpdateInvoiceRequiresTemplate()
    {
        $invoice = factory(Invoice::class)->create([
            'invoice_date' => '1988-07-18',
            'due_date' => '1988-08-18',
        ]);

        $invoice2 = factory(Invoice::class)->raw([
            'invoice_template_id' => '',
            'items' => [
                factory(InvoiceItem::class)->raw()
            ]
        ]);

        $response = $this->json('PUT', 'api/invoices/'.$invoice->id, $invoice2);

        $response->assertStatus(422)->assertJsonValidationErrors(['invoice_template_id']);
    }

    /** @test */
    public function testUpdateInvoiceRequiresItems()
    {
        $invoice = factory(Invoice::class)->create([
            'invoice_date' => '1988-07-18',
            'due_date' => '1988-08-18',
        ]);

        $invoice2 = factory(Invoice::class)->raw(['items' => []]);

        $response = $this->json('PUT', 'api/invoices/'.$invoice->id, $invoice2);

        $response->assertStatus(422)->assertJsonValidationErrors(['items']);
    }

    /** @test */
    public function testUpdateInvoiceRequiresItemsName()
    {
        $invoice = factory(Invoice::class)->create([
            'invoice_date' => '1988-07-18',
            'due_date' => '1988-08-18',
        ]);

        $invoice2 = factory(Invoice::class)->raw([
            'items' => [factory(InvoiceItem::class)->raw(['name' => ''])]
        ]);

        $response = $this->json('PUT', 'api/invoices/'.$invoice->id, $invoice2);

        $response->assertStatus(422)->assertJsonValidationErrors(['items.0.name']);
    }

    /** @test */
    public function testUpdateInvoiceRequiresItemsQuantity()
    {
        $invoice = factory(Invoice::class)->create([
            'invoice_date' => '1988-07-18',
            'due_date' => '1988-08-18',
        ]);

        $invoice2 = factory(Invoice::class)->raw([
            'items' => [factory(InvoiceItem::class)->raw(['quantity' => null])]
        ]);

        $response = $this->json('PUT', 'api/invoices/'.$invoice->id, $invoice2);

        $response->assertStatus(422)->assertJsonValidationErrors(['items.0.quantity']);
    }

    /** @test */
    public function testUpdateInvoiceRequiresItemsPrice()
    {
        $invoice = factory(Invoice::class)->create([
            'invoice_date' => '1988-07-18',
            'due_date' => '1988-08-18',
        ]);

        $invoice2 = factory(Invoice::class)->raw([
            'items' => [factory(InvoiceItem::class)->raw(['price' => null])]
        ]);

        $response = $this->json('PUT', 'api/invoices/'.$invoice->id, $invoice2);

        $response->assertStatus(422)->assertJsonValidationErrors(['items.0.price']);
    }

    /** @test */
    public function testDeleteInvoice()
    {
        $invoice = factory(Invoice::class)->create([
            'invoice_date' => '1988-07-18',
            'due_date' => '1988-08-18',
        ]);

        $response = $this->json('DELETE', 'api/invoices/'.$invoice->id);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $deleted = Invoice::find($invoice->id);
        $this->assertNull($deleted);
    }

    /** @test */
    public function testSendInvoiceToCustomer()
    {
        $invoices = factory(Invoice::class)->create([
            'invoice_date' => '1988-07-18',
            'due_date' => '1988-08-18',
        ]);

        $data = [
            'id' => $invoices->id
        ];

        $response = $this->json('POST', 'api/invoices/send', $data);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $invoice2 = Invoice::find($invoices->id);
        $this->assertEquals($invoice2->status, Invoice::STATUS_SENT);
    }

    /** @test */
    public function testInvoiceMarkAsPaid()
    {
        $invoice = factory(Invoice::class)->create([
            'invoice_date' => '1988-07-18',
            'due_date' => '1988-08-18',
        ]);

        $data = [
            'id' => $invoice->id
        ];

        $response = $this->json('POST', 'api/invoices/mark-as-paid', $data);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $invoice2 = Invoice::find($invoice->id);
        $this->assertEquals($invoice2->status, Invoice::STATUS_PAID);
    }

    /** @test */
    public function testInvoiceMarkAsSent()
    {
        $invoice = factory(Invoice::class)->create([
            'invoice_date' => '1988-07-18',
            'due_date' => '1988-08-18',
        ]);

        $data = [
            'id' => $invoice->id
        ];

        $response = $this->json('POST', 'api/invoices/mark-as-sent', $data);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $invoice2 = Invoice::find($invoice->id);
        $this->assertEquals($invoice2->status, Invoice::STATUS_SENT);
    }

    /** @test */
    public function testSearchInvoices()
    {
        $filters = [
            'page' => 1,
            'limit' => 15,
            'search' => 'doe',
            'status' => Invoice::STATUS_DRAFT,
            'from_date' => '01/09/2019',
            'to_date' => '11/09/2019',
            'invoice_number' => '000012'
        ];

        $queryString = http_build_query($filters, '', '&');

        $response = $this->json('GET', 'api/invoices?'.$queryString);

        $response->assertOk();
    }

    /** @test */
    public function testDeleteMultipleInvoices()
    {
        $invoices = factory(Invoice::class,3)->create([
            'invoice_date' => '1988-07-18',
            'due_date' => '1988-08-18',
        ]);

        $ids = $invoices->pluck('id');

        $data = [
            'id' => $ids,
            'type' => 'invoice'
        ];

        $response = $this->json('POST', 'api/invoices/delete', $data);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true
            ]);
    }
}
