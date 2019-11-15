<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crater\Item;
use Crater\User;
use Crater\Tax;
use Laravel\Passport\Passport;
use SettingsSeeder;

class ItemTest extends TestCase
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
    public function testGetItems()
    {
        $response = $this->json('GET', 'api/items?page=1');

        $response->assertOk();
    }

    /** @test */
    public function testCreateItem()
    {
        $item = factory(Item::class)->raw([
            'taxes' => [
                factory(Tax::class)->raw(),
                factory(Tax::class)->raw()
            ]
        ]);
        $response = $this->json('POST', 'api/items', $item);

        $response->assertOk();
    }

    /** @test */
    public function testItemNameRequired()
    {
        $item = factory(Item::class)->raw(['name' => '']);

        $response = $this->json('POST', 'api/items', $item);

        $response->assertStatus(422)->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function testItemPriceRequired()
    {
        $item = factory(Item::class)->raw(['price' => '']);

        $response = $this->json('POST', 'api/items', $item);

        $response->assertStatus(422)->assertJsonValidationErrors(['price']);
    }

    /** @test */
    public function testGetEditItemData()
    {
        $item = factory(Item::class)->create();

        $response = $this->json('GET', 'api/items/'.$item->id.'/edit');

        $response
            ->assertOk()
            ->assertJson([
                'item' => $item->toArray()
            ]);
    }

    /** @test */
    public function testUpdateItem()
    {
        $item = factory(Item::class)->create();
        $item2 = factory(Item::class)->raw([
            'taxes' => [
                factory(Tax::class)->raw(['tax_type_id' => $item->taxes[0]->tax_type_id]),
                factory(Tax::class)->raw(['tax_type_id' => $item->taxes[1]->tax_type_id])
            ]
        ]);

        $response = $this->json('PUT', 'api/items/'.$item->id, $item2);

        $item3 = $response->decodeResponseJson()['item'];

        $response->assertOk();
    }

    /** @test */
    public function testUpdateItemNameRequired()
    {
        $item = factory(Item::class)->create();
        $item2 = factory(Item::class)->raw(['name' => '']);

        $response = $this->json('PUT', 'api/items/'.$item->id, $item2);

        $response->assertStatus(422)->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function testUpdateItemPriceRequired()
    {
        $item = factory(Item::class)->create();
        $item2 = factory(Item::class)->raw(['price' => '']);

        $response = $this->json('PUT', 'api/items/'.$item->id, $item2);

        $response->assertStatus(422)->assertJsonValidationErrors(['price']);
    }

    /** @test */
    public function testDeleteItem()
    {
        $item = factory(Item::class)->create();

        $response = $this->json('DELETE', 'api/items/'.$item->id);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true
            ]);
    }

    /** @test */
    public function testSearchItems()
    {
        $filters = [
            'page' => 1,
            'limit' => 15,
            'search' => 'doe',
            'price' => 6,
            'unit' => 'kg'
        ];

        $queryString = http_build_query($filters, '', '&');

        $response = $this->json('GET', 'api/items?'.$queryString);

        $response->assertOk();
    }

    /** @test */
    public function testDeleteMultipleItems()
    {
        $items = factory(Item::class, 3)->create();

        $ids = $items->pluck('id');

        $data = [
            'id' => $ids,
            'type' => 'item'
        ];

        $response = $this->json('POST', 'api/items/delete', $data);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true
            ]);
    }
}
