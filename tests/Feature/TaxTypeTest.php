<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crater\User;
use Crater\TaxType;
use Laravel\Passport\Passport;
use SettingsSeeder;

class TaxTypeTest extends TestCase
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
    public function testGetTaxTypes()
    {
        $response = $this->json('GET', 'api/tax-types');

        $response->assertOk();
    }

    /** @test */
    public function testCreateTaxType()
    {
        $taxType = factory(TaxType::class)->raw();

        $response = $this->json('POST', 'api/tax-types', $taxType);

        $response->assertOk()
            ->assertJson([
                'taxType' => $taxType
            ]);
    }

    /** @test */
    public function testCreateTaxTypeRequiresName()
    {
        $taxType = factory(TaxType::class)->raw(['name' => '']);

        $response = $this->json('POST', 'api/tax-types', $taxType);

        $response->assertStatus(422)->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function testCreateTaxTypeRequiresPercent()
    {
        $taxType = factory(TaxType::class)->raw(['percent' => '']);

        $response = $this->json('POST', 'api/tax-types', $taxType);

        $response->assertStatus(422)->assertJsonValidationErrors(['percent']);
    }

    /** @test */
    public function testGetEditTaxTypeData()
    {
        $taxType = factory(TaxType::class)->create();

        $response = $this->json('GET', 'api/tax-types/'.$taxType->id.'/edit');

        $response->assertOk()
            ->assertJson([
                'taxType' => $taxType->toArray()
            ]);
    }

    /** @test */
    public function testUpdateTaxType()
    {
        $taxType = factory(TaxType::class)->create();

        $taxType2 = factory(TaxType::class)->raw();

        $response = $this->json('PUT', 'api/tax-types/'.$taxType->id, $taxType2);

        $response->assertOk()
            ->assertJson([
                'taxType' => $taxType2
            ]);
    }

    /** @test */
    public function testUpdateTaxTypeRequiresName()
    {
        $taxType = factory(TaxType::class)->create();
        $taxType2 = factory(TaxType::class)->raw(['name' => '']);

        $response = $this->json('PUT', 'api/tax-types/'.$taxType->id, $taxType2);

        $response->assertStatus(422)->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function testUpdateTaxTypeRequiresPercent()
    {
        $taxType = factory(TaxType::class)->create();
        $taxType2 = factory(TaxType::class)->raw(['percent' => '']);

        $response = $this->json('PUT', 'api/tax-types/'.$taxType->id, $taxType2);

        $response->assertStatus(422)->assertJsonValidationErrors(['percent']);
    }

    /** @test */
    public function testDeleteTaxType()
    {
        $taxType = factory(TaxType::class)->create();

        $response = $this->json('DELETE', 'api/tax-types/'.$taxType->id);

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        $this->assertNull(TaxType::find($taxType->id));
    }
}
