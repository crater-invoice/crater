<?php

use Crater\Http\Controllers\V1\Admin\Settings\TaxTypesController;
use Crater\Http\Requests\TaxTypeRequest;
use Crater\Models\TaxType;
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

test('get tax types', function () {
    $response = getJson('api/v1/tax-types');

    $response->assertOk();
});

test('create tax type', function () {
    $taxType = TaxType::factory()->raw();

    postJson('api/v1/tax-types', $taxType);

    $this->assertDatabaseHas('tax_types', $taxType);
});

test('store validates using a form request', function () {
    $this->assertActionUsesFormRequest(
        TaxTypesController::class,
        'store',
        TaxTypeRequest::class
    );
});

test('get tax type', function () {
    $taxType = TaxType::factory()->create();

    $response = getJson('api/v1/tax-types/'.$taxType->id);

    $response->assertOk();
});

test('update tax type', function () {
    $taxType = TaxType::factory()->create();

    $taxType1 = TaxType::factory()->raw();

    $response = putJson('api/v1/tax-types/'.$taxType->id, $taxType1);

    $response->assertOk();
});

test('update validates using a form request', function () {
    $this->assertActionUsesFormRequest(
        TaxTypesController::class,
        'update',
        TaxTypeRequest::class
    );
});

test('delete tax type', function () {
    $taxType = TaxType::factory()->create();

    $response = deleteJson('api/v1/tax-types/'.$taxType->id);

    $response->assertOk()
        ->assertJson([
            'success' => true,
        ]);

    $this->assertDeleted($taxType);
});


test('create negative tax type', function () {
    $taxType = TaxType::factory()->raw([
        'percent' => -9.99
    ]);

    postJson('api/v1/tax-types', $taxType)
        ->assertStatus(201);

    $this->assertDatabaseHas('tax_types', $taxType);
});
