<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crater\User;
use Crater\Estimate;
use Crater\Invoice;
use Crater\EstimateItem;
use Crater\Tax;
use Laravel\Passport\Passport;
use SettingsSeeder;

class EstimateTest extends TestCase
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
    public function testGetEstimates()
    {
        $response = $this->json('GET', 'api/estimates?page=1');

        $response->assertOk();
    }

    /** @test */
    public function testGetCreateEstimateData()
    {
        $response = $this->json('GET', 'api/estimates/create');

        $response->assertOk();
    }

    /** @test */
    public function testCreateEstimate()
    {
        $estimate = factory(Estimate::class)->raw([
            'items' => [
                factory(EstimateItem::class)->raw()
            ],
            'taxes' => [
                factory(Tax::class)->raw()
            ]
        ]);

        $response = $this->json('POST', 'api/estimates', $estimate);

        $newEstimate = $response->decodeResponseJson()['estimate'];

        $response->assertOk();
        $this->assertEquals($estimate['estimate_number'], $newEstimate['estimate_number']);
        $this->assertEquals($estimate['user_id'], $newEstimate['user_id']);
        $this->assertEquals($estimate['estimate_template_id'], $newEstimate['estimate_template_id']);
        $this->assertEquals($estimate['sub_total'], $newEstimate['sub_total']);
        $this->assertEquals($estimate['total'], $newEstimate['total']);
        $this->assertEquals($estimate['discount'], $newEstimate['discount']);
        $this->assertEquals($estimate['discount_type'], $newEstimate['discount_type']);
        $this->assertEquals($estimate['discount_val'], $newEstimate['discount_val']);
        $this->assertEquals($estimate['tax'], $newEstimate['tax']);
        $this->assertEquals($estimate['notes'], $newEstimate['notes']);
        $this->assertEquals($estimate['items'][0]['name'], $newEstimate['items'][0]['name']);
        $this->assertEquals($estimate['items'][0]['description'], $newEstimate['items'][0]['description']);
        $this->assertEquals($estimate['items'][0]['price'], $newEstimate['items'][0]['price']);
        $this->assertEquals($estimate['items'][0]['quantity'], $newEstimate['items'][0]['quantity']);
        $this->assertEquals($estimate['items'][0]['discount_type'], $newEstimate['items'][0]['discount_type']);
        $this->assertEquals($estimate['items'][0]['discount'], $newEstimate['items'][0]['discount']);
        $this->assertEquals($estimate['items'][0]['total'], $newEstimate['items'][0]['total']);
        $this->assertEquals($estimate['taxes'][0]['tax_type_id'], $newEstimate['taxes'][0]['tax_type_id']);
        $this->assertEquals($estimate['taxes'][0]['percent'], $newEstimate['taxes'][0]['percent']);
        $this->assertEquals($estimate['taxes'][0]['amount'], $newEstimate['taxes'][0]['amount']);
        $this->assertEquals($estimate['taxes'][0]['name'], $newEstimate['taxes'][0]['name']);
        $this->assertEquals($newEstimate['id'], $newEstimate['taxes'][0]['estimate_id']);
    }

    /** @test */
    public function testCreateEstimateEstimateDateRequired()
    {
        $estimate = factory(Estimate::class)->raw([
            'estimate_date' => '',
            'items' => [
                factory(EstimateItem::class)->raw()
            ]
        ]);

        $response = $this->json('POST', 'api/estimates', $estimate);

        $response->assertStatus(422)->assertJsonValidationErrors(['estimate_date']);
    }

    /** @test */
    public function testCreateEstimateExpiryDateRequired()
    {
        $estimate = factory(Estimate::class)->raw([
            'expiry_date' => '',
            'items' => [
                factory(EstimateItem::class)->raw()
            ]
        ]);

        $response = $this->json('POST', 'api/estimates', $estimate);

        $response->assertStatus(422)->assertJsonValidationErrors(['expiry_date']);
    }

    /** @test */
    public function testCreateEstimateRequiresEstimateNumber()
    {
        $estimate = factory(Estimate::class)->raw([
            'estimate_number' => '',
            'items' => [
                factory(EstimateItem::class)->raw()
            ]
        ]);

        $response = $this->json('POST', 'api/estimates', $estimate);

        $response->assertStatus(422)->assertJsonValidationErrors(['estimate_number']);
    }

    /** @test */
    public function testCreateEstimateRequiresUser()
    {
        $estimate = factory(Estimate::class)->raw([
            'user_id' => '',
            'items' => [
                factory(EstimateItem::class)->raw()
            ]
        ]);

        $response = $this->json('POST', 'api/estimates', $estimate);

        $response->assertStatus(422)->assertJsonValidationErrors(['user_id']);
    }

    /** @test */
    public function testCreateEstimateRequiresDiscount()
    {
        $estimate = factory(Estimate::class)->raw([
            'discount' => '',
            'items' => [
                factory(EstimateItem::class)->raw()
            ]
        ]);

        $response = $this->json('POST', 'api/estimates', $estimate);

        $response->assertStatus(422)->assertJsonValidationErrors(['discount']);
    }

    /** @test */
    public function testCreateEstimateRequiresTemplate()
    {
        $estimate = factory(Estimate::class)->raw([
            'estimate_template_id' => '',
            'items' => [
                factory(EstimateItem::class)->raw()
            ]
        ]);

        $response = $this->json('POST', 'api/estimates', $estimate);

        $response->assertStatus(422)->assertJsonValidationErrors(['estimate_template_id']);
    }

    /** @test */
    public function testCreateEstimateRequiresItems()
    {
        $estimate = factory(Estimate::class)->raw(['items' => []]);

        $response = $this->json('POST', 'api/estimates', $estimate);

        $response->assertStatus(422)->assertJsonValidationErrors(['items']);
    }

    /** @test */
    public function testCreateEstimateRequiresItemsName()
    {
        $estimate = factory(Estimate::class)->raw([
            'items' => [factory(EstimateItem::class)->raw(['name' => ''])]
        ]);

        $response = $this->json('POST', 'api/estimates', $estimate);

        $response->assertStatus(422)->assertJsonValidationErrors(['items.0.name']);
    }

    /** @test */
    public function testCreateEstimateRequiresItemsQuantity()
    {
        $estimate = factory(Estimate::class)->raw([
            'items' => [factory(EstimateItem::class)->raw(['quantity' => null])]
        ]);

        $response = $this->json('POST', 'api/estimates', $estimate);

        $response->assertStatus(422)->assertJsonValidationErrors(['items.0.quantity']);
    }

    /** @test */
    public function testCreateEstimateRequiresItemsPrice()
    {
        $estimate = factory(Estimate::class)->raw([
            'items' => [factory(EstimateItem::class)->raw(['price' => null])]
        ]);

        $response = $this->json('POST', 'api/estimates', $estimate);

        $response->assertStatus(422)->assertJsonValidationErrors(['items.0.price']);
    }

    /** @test */
    public function testGetEditEstimateData()
    {
        $estimate = factory(Estimate::class)->create([
            'estimate_date' => '1988-07-18',
            'expiry_date' => '1988-08-18',
        ]);

        $response = $this->json('GET', 'api/estimates/'.$estimate->id.'/edit');

        $response->assertOk();
    }

    /** @test */
    public function testUpdateEstimate()
    {
        $estimate = factory(Estimate::class)->create([
            'estimate_date' => '1988-07-18',
            'expiry_date' => '1988-08-18',
        ]);

        $estimate2 = factory(Estimate::class)->raw([
            'items' => [
                factory(EstimateItem::class)->raw()
            ],
            'taxes' => [
                factory(Tax::class)->raw([
                    'tax_type_id' => $estimate->taxes[0]->tax_type_id
                ])
            ]
        ]);

        $response = $this->json('PUT', 'api/estimates/'.$estimate->id, $estimate2);

        $newEstimate = $response->decodeResponseJson()['estimate'];

        $response->assertOk();
        $this->assertEquals($estimate2['estimate_number'], $newEstimate['estimate_number']);
        $this->assertEquals($estimate2['user_id'], $newEstimate['user_id']);
        $this->assertEquals($estimate2['estimate_template_id'], $newEstimate['estimate_template_id']);
        $this->assertEquals($estimate2['sub_total'], $newEstimate['sub_total']);
        $this->assertEquals($estimate2['total'], $newEstimate['total']);
        $this->assertEquals($estimate2['discount'], $newEstimate['discount']);
        $this->assertEquals($estimate2['discount_type'], $newEstimate['discount_type']);
        $this->assertEquals($estimate2['discount_val'], $newEstimate['discount_val']);
        $this->assertEquals($estimate2['tax'], $newEstimate['tax']);
        $this->assertEquals($estimate2['notes'], $newEstimate['notes']);
        $this->assertEquals($estimate2['items'][0]['name'], $newEstimate['items'][0]['name']);
        $this->assertEquals($estimate2['items'][0]['description'], $newEstimate['items'][0]['description']);
        $this->assertEquals($estimate2['items'][0]['price'], $newEstimate['items'][0]['price']);
        $this->assertEquals($estimate2['items'][0]['quantity'], $newEstimate['items'][0]['quantity']);
        $this->assertEquals($estimate2['items'][0]['discount_type'], $newEstimate['items'][0]['discount_type']);
        $this->assertEquals($estimate2['items'][0]['discount'], $newEstimate['items'][0]['discount']);
        $this->assertEquals($estimate2['items'][0]['total'], $newEstimate['items'][0]['total']);
        $this->assertEquals($estimate2['taxes'][0]['tax_type_id'], $newEstimate['taxes'][0]['tax_type_id']);
        $this->assertEquals($estimate2['taxes'][0]['percent'], $newEstimate['taxes'][0]['percent']);
        $this->assertEquals($estimate2['taxes'][0]['amount'], $newEstimate['taxes'][0]['amount']);
        $this->assertEquals($estimate2['taxes'][0]['name'], $newEstimate['taxes'][0]['name']);
        $this->assertEquals($newEstimate['id'], $newEstimate['taxes'][0]['estimate_id']);
    }


    /** @test */
    public function testUpdateEstimateEstimateDateRequired()
    {
        $estimate = factory(Estimate::class)->create([
            'estimate_date' => '1988-07-18',
            'expiry_date' => '1988-08-18',
        ]);

        $estimate2 = factory(Estimate::class)->raw([
            'estimate_date' => '',
            'items' => [
                factory(EstimateItem::class)->raw()
            ]
        ]);

        $response = $this->json('PUT', 'api/estimates/'.$estimate->id, $estimate2);

        $response->assertStatus(422)->assertJsonValidationErrors(['estimate_date']);
    }

    /** @test */
    public function testUpdateEstimateExpiryDateRequired()
    {
        $estimate = factory(Estimate::class)->create([
            'estimate_date' => '1988-07-18',
            'expiry_date' => '1988-08-18',
        ]);

        $estimate2 = factory(Estimate::class)->raw([
            'expiry_date' => '',
            'items' => [
                factory(EstimateItem::class)->raw()
            ]
        ]);

        $response = $this->json('PUT', 'api/estimates/'.$estimate->id, $estimate2);

        $response->assertStatus(422)->assertJsonValidationErrors(['expiry_date']);
    }

    /** @test */
    public function testUpdateEstimateRequiresEstimateNumber()
    {
        $estimate = factory(Estimate::class)->create([
            'estimate_date' => '1988-07-18',
            'expiry_date' => '1988-08-18',
        ]);

        $estimate2 = factory(Estimate::class)->raw([
            'estimate_number' => '',
            'items' => [
                factory(EstimateItem::class)->raw()
            ]
        ]);

        $response = $this->json('PUT', 'api/estimates/'.$estimate->id, $estimate2);

        $response->assertStatus(422)->assertJsonValidationErrors(['estimate_number']);
    }

    /** @test */
    public function testUpdateEstimateRequiresUser()
    {
        $estimate = factory(Estimate::class)->create([
            'estimate_date' => '1988-07-18',
            'expiry_date' => '1988-08-18',
        ]);

        $estimate2 = factory(Estimate::class)->raw([
            'user_id' => '',
            'items' => [
                factory(EstimateItem::class)->raw()
            ]
        ]);

        $response = $this->json('PUT', 'api/estimates/'.$estimate->id, $estimate2);

        $response->assertStatus(422)->assertJsonValidationErrors(['user_id']);
    }

    /** @test */
    public function testUpdateEstimateRequiresDiscount()
    {
        $estimate = factory(Estimate::class)->create([
            'estimate_date' => '1988-07-18',
            'expiry_date' => '1988-08-18',
        ]);

        $estimate2 = factory(Estimate::class)->raw([
            'discount' => '',
            'items' => [
                factory(EstimateItem::class)->raw()
            ]
        ]);

        $response = $this->json('PUT', 'api/estimates/'.$estimate->id, $estimate2);

        $response->assertStatus(422)->assertJsonValidationErrors(['discount']);
    }

    /** @test */
    public function testUpdateEstimateRequiresTemplate()
    {
        $estimate = factory(Estimate::class)->create([
            'estimate_date' => '1988-07-18',
            'expiry_date' => '1988-08-18',
        ]);

        $estimate2 = factory(Estimate::class)->raw([
            'estimate_template_id' => '',
            'items' => [
                factory(EstimateItem::class)->raw()
            ]
        ]);

        $response = $this->json('PUT', 'api/estimates/'.$estimate->id, $estimate2);

        $response->assertStatus(422)->assertJsonValidationErrors(['estimate_template_id']);
    }

    /** @test */
    public function testUpdateEstimateRequiresItems()
    {
        $estimate = factory(Estimate::class)->create([
            'estimate_date' => '1988-07-18',
            'expiry_date' => '1988-08-18',
        ]);

        $estimate2 = factory(Estimate::class)->raw(['items' => []]);

        $response = $this->json('PUT', 'api/estimates/'.$estimate->id, $estimate2);

        $response->assertStatus(422)->assertJsonValidationErrors(['items']);
    }

    /** @test */
    public function testUpdateEstimateRequiresItemsName()
    {
        $estimate = factory(Estimate::class)->create([
            'estimate_date' => '1988-07-18',
            'expiry_date' => '1988-08-18',
        ]);

        $estimate2 = factory(Estimate::class)->raw([
            'items' => [factory(EstimateItem::class)->raw(['name' => ''])]
        ]);

        $response = $this->json('PUT', 'api/estimates/'.$estimate->id, $estimate2);

        $response->assertStatus(422)->assertJsonValidationErrors(['items.0.name']);
    }

    /** @test */
    public function testUpdateEstimateRequiresItemsQuantity()
    {
        $estimate = factory(Estimate::class)->create([
            'estimate_date' => '1988-07-18',
            'expiry_date' => '1988-08-18',
        ]);

        $estimate2 = factory(Estimate::class)->raw([
            'items' => [factory(EstimateItem::class)->raw(['quantity' => null])]
        ]);

        $response = $this->json('PUT', 'api/estimates/'.$estimate->id, $estimate2);

        $response->assertStatus(422)->assertJsonValidationErrors(['items.0.quantity']);
    }

    /** @test */
    public function testUpdateEstimateRequiresItemsPrice()
    {
        $estimate = factory(Estimate::class)->create([
            'estimate_date' => '1988-07-18',
            'expiry_date' => '1988-08-18',
        ]);

        $estimate2 = factory(Estimate::class)->raw([
            'items' => [factory(EstimateItem::class)->raw(['price' => null])]
        ]);

        $response = $this->json('PUT', 'api/estimates/'.$estimate->id, $estimate2);

        $response->assertStatus(422)->assertJsonValidationErrors(['items.0.price']);
    }

    /** @test */
    public function testDeleteEstimate()
    {
        $estimate = factory(Estimate::class)->create([
            'estimate_date' => '1988-07-18',
            'expiry_date' => '1988-08-18',
        ]);

        $response = $this->json('DELETE', 'api/estimates/'.$estimate->id);

        $response
            ->assertOk()
            ->assertJson([
                'success' => 'Estimate deleted successfully'
            ]);

        $deleted = Estimate::find($estimate->id);

        $this->assertNull($deleted);
    }

    /** @test */
    public function testSearchEstimates()
    {
        $filters = [
            'page' => 1,
            'limit' => 15,
            'search' => 'doe',
            'from_date' => '01/09/2019',
            'to_date' => '11/09/2019',
            'estimate_number' => '000003'
        ];

        $queryString = http_build_query($filters, '', '&');

        $response = $this->json('GET', 'api/estimates?'.$queryString);

        $response->assertOk();
    }

    /** @test */
    public function testSendEstimateToCustomer()
    {
        $estimate = factory(Estimate::class)->create([
            'estimate_date' => '1988-07-18',
            'expiry_date' => '1988-08-18',
        ]);

        $data = [
            'id' => $estimate->id
        ];

        $response = $this->json('POST', 'api/estimates/send', $data);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true
            ]);
    }

    /** @test */
    public function testEstimateMarkAsAccepted()
    {
        $estimate = factory(Estimate::class)->create([
            'estimate_date' => '1988-07-18',
            'expiry_date' => '1988-08-18',
        ]);

        $data = [
            'id' => $estimate->id
        ];

        $response = $this->json('POST', 'api/estimates/accept', $data);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $estimate2 = Estimate::find($estimate->id);
        $this->assertEquals($estimate2->status, Estimate::STATUS_ACCEPTED);
    }

    /** @test */
    public function testEstimateMarkAsRejected()
    {
        $estimate = factory(Estimate::class)->create([
            'estimate_date' => '1988-07-18',
            'expiry_date' => '1988-08-18',
        ]);

        $data = [
            'id' => $estimate->id
        ];

        $response = $this->json('POST', 'api/estimates/reject', $data);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $estimate2 = Estimate::find($estimate->id);
        $this->assertEquals($estimate2->status, Estimate::STATUS_REJECTED);
    }

    /** @test */
    public function testCreateInvoiceFromEstimate()
    {
        $estimate = factory(Estimate::class)->create([
            'estimate_date' => '1988-07-18',
            'expiry_date' => '1988-08-18',
        ]);

        $invoice = factory(Invoice::class)->create([
            'invoice_date' => '1988-07-18',
            'due_date' => '1988-08-18',
        ]);

        $response = $this->json('POST', 'api/estimates/'.$estimate->id.'/convert-to-invoice');

        $response->assertOk();
    }

    /** @test */
    public function testDeleteMultipleEstimates()
    {
        $estimates = factory(Estimate::class, 3)->create([
            'estimate_date' => '1988-07-18',
            'expiry_date' => '1988-08-18',
        ]);

        $ids = $estimates->pluck('id');

        $data = [
            'id' => $ids
        ];

        $response = $this->json('POST', 'api/estimates/delete', $data);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true
            ]);
    }
}
