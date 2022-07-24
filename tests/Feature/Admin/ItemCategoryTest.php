<?php

use Crater\Http\Controllers\V1\Admin\Item\ItemCategoriesController;
use Crater\Http\Requests\ItemCategoryRequest;
use Crater\Models\ItemCategory;
use Crater\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\deleteJson;
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

test('get item categories', function () {
    $response = getJson('api/v1/items/categories');

    $response->assertOk();
});

test('create category', function () {
    $category = ItemCategory::factory()->raw();


    $response = postJson('api/v1/items/categories', $category);

    $response->assertStatus(201);

    $this->assertDatabaseHas('item_categories', [
        'name' => $category['name'],
        'number' => $category['number'],
    ]);
});

test('store validates using a form request', function () {
    $this->assertActionUsesFormRequest(
        ItemCategoriesController::class,
        'store',
        ItemCategoryRequest::class
    );
});

test('get category', function () {
    $category = ItemCategory::factory()->create();

    getJson("api/v1/items/categories/{$category->id}")->assertOk();
});

test('update category', function () {
    $category = ItemCategory::factory()->create();

    $category2 = ItemCategory::factory()->raw();

    putJson('api/v1/items/categories/'.$category->id, $category2)->assertOk();

    $this->assertDatabaseHas('item_categories', [
        'id' => $category->id,
        'name' => $category2['name'],
        'number' => $category2['number'],
    ]);
});

test('update validates using a form request', function () {
    $this->assertActionUsesFormRequest(
        ItemCategoriesController::class,
        'update',
        ItemCategoryRequest::class
    );
});

test('delete category', function () {
    $category = ItemCategory::factory()->create();

    deleteJson('api/v1/items/categories/'.$category->id)
        ->assertOk()
        ->assertJson([
            'success' => true,
        ]);

    $this->assertDeleted($category);
});
