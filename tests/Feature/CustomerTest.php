<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crater\User;
use Laravel\Passport\Passport;
use SettingsSeeder;

class CustomerTest extends TestCase
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
    public function testGetCustomers()
    {
        $response = $this->json('GET', 'api/customers?page=1');

        $response->assertOk();
    }

    /** @test */
    public function testCreateCustomer()
    {
        $customer = factory(User::class)->raw([
            'password' => 'secret',
            'role' => 'customer'
        ]);

        $response = $this->json('POST', 'api/customers', $customer);

        $newCustomer = $response->decodeResponseJson()['customer'];

        $this->assertEquals($customer['name'], $newCustomer['name']);
        $this->assertEquals($customer['email'], $newCustomer['email']);
        $this->assertEquals($customer['role'], $newCustomer['role']);
        $this->assertEquals($customer['phone'], $newCustomer['phone']);
        $this->assertEquals($customer['company_name'], $newCustomer['company_name']);
        $this->assertEquals($customer['contact_name'], $newCustomer['contact_name']);
        $this->assertEquals($customer['website'], $newCustomer['website']);
        $this->assertEquals($customer['enable_portal'], $newCustomer['enable_portal']);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true
            ]);
    }

    /** @test */
    public function testCustomerNameRequired()
    {
        $customer = factory(User::class)->raw([
            'name' => '',
            'password' => 'secret',
            'role' => 'customer'
        ]);

        $response = $this->json('POST', 'api/customers', $customer);

        $response->assertStatus(422)->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function testGetUpdateCustomerData()
    {
        $customer = factory(User::class)->create(['role' => 'customer']);

        $response = $this->json('GET', 'api/customers/'.$customer->id.'/edit');

        $newCustomer = $response->decodeResponseJson()['customer'];

        $this->assertEquals($customer->name, $newCustomer['name']);
        $this->assertEquals($customer->email, $newCustomer['email']);
        $this->assertEquals($customer->role, $newCustomer['role']);
        $this->assertEquals($customer->phone, $newCustomer['phone']);
        $this->assertEquals($customer->company_name, $newCustomer['company_name']);
        $this->assertEquals($customer->contact_name, $newCustomer['contact_name']);
        $this->assertEquals($customer->website, $newCustomer['website']);
        $this->assertEquals($customer->enable_portal, $newCustomer['enable_portal']);
        $response->assertOk();
    }

    /** @test */
    public function testUpdateCustomer()
    {
        $customer = factory(User::class)->create(['role' => 'customer']);
        $customer2 = factory(User::class)->raw(['role' => 'customer']);

        $response = $this->json('PUT', 'api/customers/'.$customer->id, $customer2);

        $newCustomer = $response->decodeResponseJson()['customer'];

        $this->assertEquals($customer2['name'], $newCustomer['name']);
        $this->assertEquals($customer2['email'], $newCustomer['email']);
        $this->assertEquals($customer2['role'], $newCustomer['role']);
        $this->assertEquals($customer2['phone'], $newCustomer['phone']);
        $this->assertEquals($customer2['company_name'], $newCustomer['company_name']);
        $this->assertEquals($customer2['contact_name'], $newCustomer['contact_name']);
        $this->assertEquals($customer2['website'], $newCustomer['website']);
        $this->assertEquals($customer2['enable_portal'], $newCustomer['enable_portal']);
        $response
            ->assertOk()
            ->assertJson([
                'success' => true
            ]);
    }

    /** @test */
    public function testDeleteCustomer()
    {
        $customer = factory(User::class)->create(['role' => 'customer']);

        $response = $this->json('DELETE', 'api/customers/'.$customer->id);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $deletedCustomer = User::find($customer->id);

        $this->assertNull($deletedCustomer);
    }

    /** @test */
    public function testSearchCustomers()
    {
        $filters = [
            'page' => 1,
            'limit' => 15,
            'search' => 'doe',
            'email' => '.com'
        ];

        $queryString = http_build_query($filters, '', '&');

        $response = $this->json('GET', 'api/customers?'.$queryString);

        $response->assertOk();
    }

    /** @test */
    public function testDeleteMultipleCustomer()
    {
        $customers = factory(User::class, 3)->create(['role' => 'customer']);

        $ids = $customers->pluck('id');

        $data = [
            'id' => $ids
        ];

        $response = $this->json('POST', 'api/customers/delete', $data);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true
            ]);
    }
}
