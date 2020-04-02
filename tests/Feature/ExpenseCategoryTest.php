<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crater\User;
use Crater\ExpenseCategory;
use Laravel\Passport\Passport;
use SettingsSeeder;

class ExpenseCategoryTest extends TestCase
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
    public function testGetCategories()
    {
        $response = $this->json('GET', 'api/categories');

        $response->assertOk();
    }

    /** @test */
    public function testCreateCategory()
    {
        $category = factory(ExpenseCategory::class)->raw();

        $response = $this->json('POST', 'api/categories', $category);

        $category2 = $response->decodeResponseJson()['category'];

        $response->assertOk();
        $this->assertEquals($category['name'], $category2['name']);
        $this->assertEquals($category['description'], $category2['description']);
    }

    /** @test */
    public function testCreateCategoryRequiresName()
    {
        $category = factory(ExpenseCategory::class)->raw([
            'name' => ''
        ]);

        $response = $this->json('POST', 'api/categories', $category);

        $response->assertStatus(422)->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function testGetEditCategoryData()
    {
        $category = factory(ExpenseCategory::class)->create();

        $response = $this->json('GET', 'api/categories/'.$category->id.'/edit');

        $category2 = $response->decodeResponseJson()['category'];

        $response->assertOk();
        $this->assertEquals($category->toArray(), $category2);
    }

    /** @test */
    public function testUpdateCategory()
    {
        $category = factory(ExpenseCategory::class)->create();

        $category2 = factory(ExpenseCategory::class)->raw();

        $response = $this->json('PUT', 'api/categories/'.$category->id, $category2);

        $category3 = $response->decodeResponseJson()['category'];

        $response->assertOk();
        $this->assertEquals($category3['name'], $category2['name']);
        $this->assertEquals($category3['description'], $category2['description']);
    }

    /** @test */
    public function testUpdateCategoryRequiresName()
    {
        $category = factory(ExpenseCategory::class)->create();

        $category2 = factory(ExpenseCategory::class)->raw([
            'name' => ''
        ]);

        $response = $this->json('PUT', 'api/categories/'.$category->id, $category2);

        $response->assertStatus(422)->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function testDeleteCategory()
    {
        $category = factory(ExpenseCategory::class)->create();

        $response = $this->json('DELETE', 'api/categories/'.$category->id);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true
            ]);
    }
}
