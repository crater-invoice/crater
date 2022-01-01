<?php

use Crater\Http\Controllers\V1\Admin\CustomField\CustomFieldsController;
use Crater\Http\Requests\CustomFieldRequest;
use Crater\Models\CustomField;
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

test('get custom fields', function () {
    $response = getJson('api/v1/custom-fields?page=1');

    $response->assertOk();
});

test('create custom field', function () {
    $data = CustomField::factory()->raw();

    postJson('api/v1/custom-fields', $data)
        ->assertStatus(201);

    $this->assertDatabaseHas('custom_fields', [
        'name' => $data['name'],
        'label' => $data['label'],
        'type' => $data['type'],
        'model_type' => $data['model_type'],
        'is_required' => $data['is_required'],
    ]);
});

test('store validates using a form request', function () {
    $this->assertActionUsesFormRequest(
        CustomFieldsController::class,
        'store',
        CustomFieldRequest::class
    );
});

test('update custom field', function () {
    $customField = CustomField::factory()->create();

    $newCustomField = CustomField::factory()->raw([
        'is_required' => false,
    ]);

    putJson('api/v1/custom-fields/'.$customField->id, $newCustomField)
        ->assertStatus(200);

    $this->assertDatabaseHas('custom_fields', [
        'id' => $customField->id,
        'name' => $newCustomField['name'],
        'label' => $newCustomField['label'],
        'type' => $newCustomField['type'],
        'model_type' => $newCustomField['model_type'],
    ]);
});

test('update validates using a form request', function () {
    $this->assertActionUsesFormRequest(
        CustomFieldsController::class,
        'update',
        CustomFieldRequest::class
    );
});

test('delete custom field', function () {
    $customField = CustomField::factory()->create();

    $response = deleteJson('api/v1/custom-fields/'.$customField->id);

    $response
        ->assertOk()
        ->assertJson([
            'success' => true,
        ]);

    $this->assertDeleted($customField);
});
