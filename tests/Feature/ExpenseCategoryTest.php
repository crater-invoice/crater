<?php
use Crater\Models\User;
use Crater\Models\ExpenseCategory;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use Crater\Http\Requests\ExpenseCategoryRequest;
use Crater\Http\Controllers\V1\Expense\ExpenseCategoriesController;
use function Pest\Laravel\{postJson, putJson, getJson, deleteJson};

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

test('get categories', function () {
    $response = getJson('api/v1/categories');

    $response->assertOk();
});

test('create category', function () {
    $category = ExpenseCategory::factory()->raw();

    $response = postJson('api/v1/categories', $category);

    $response->assertStatus(200);

    $this->assertDatabaseHas('expense_categories', [
        'name' => $category['name'],
        'description' => $category['description'],
    ]);
});

test('store validates using a form request', function () {
    $this->assertActionUsesFormRequest(
        ExpenseCategoriesController::class,
        'store',
        ExpenseCategoryRequest::class
    );
});

test('get category', function () {
    $category = ExpenseCategory::factory()->create();

    getJson("api/v1/categories/{$category->id}")->assertOk();
});

test('update category', function () {
    $category = ExpenseCategory::factory()->create();

    $category2 = ExpenseCategory::factory()->raw();

    putJson('api/v1/categories/'.$category->id, $category2)->assertOk();

    $this->assertDatabaseHas('expense_categories', [
        'id' => $category->id,
        'name' => $category2['name'],
        'description' => $category2['description'],
    ]);
});

test('update validates using a form request', function () {
    $this->assertActionUsesFormRequest(
        ExpenseCategoriesController::class,
        'update',
        ExpenseCategoryRequest::class
    );
});

test('delete category', function () {
    $category = ExpenseCategory::factory()->create();

    deleteJson('api/v1/categories/'.$category->id)
        ->assertOk()
        ->assertJson([
            'success' => true
        ]);

    $this->assertDeleted($category);
});
