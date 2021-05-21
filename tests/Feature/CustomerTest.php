<?php

use Crater\Http\Controllers\V1\Customer\CustomersController;
use Crater\Http\Requests\CustomerRequest;
use Crater\Models\Invoice;
use Crater\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

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

test('get customers', function () {
    $response = getJson('api/v1/customers?page=1');

    $response->assertOk();
});

test('customer stats', function () {
    $customer = User::factory()->create([
        'role' => 'customer',
    ]);

    $invoice = Invoice::factory()->create([
        'user_id' => $customer->id,
    ]);

    $response = getJson("api/v1/customers/{$customer->id}/stats");

    $response->assertStatus(200);
});

test('create customer', function () {
    $customer = User::factory()->raw([
        'password' => 'secret',
        'role' => 'customer',
    ]);

    $response = postJson('api/v1/customers', $customer);

    $this->assertDatabaseHas('users', [
        'name' => $customer['name'],
        'email' => $customer['email'],
        'role' => $customer['role'],
        'company_id' => $customer['company_id'],
    ]);

    $response
        ->assertOk()
        ->assertJson([
            'success' => true,
        ]);
});

test('store validates using a form request', function () {
    $this->assertActionUsesFormRequest(
        CustomersController::class,
        'store',
        CustomerRequest::class
    );
});

test('get customer', function () {
    $customer = User::factory()->create([
        'role' => 'customer',
    ]);

    $response = getJson("api/v1/customers/{$customer->id}");

    $this->assertDatabaseHas('users', [
        'id' => $customer->id,
        'name' => $customer['name'],
        'email' => $customer['email'],
        'role' => $customer['role'],
        'company_id' => $customer['company_id'],
    ]);

    $response->assertOk();
});

test('update customer', function () {
    $customer = User::factory()->create([
        'role' => 'customer',
    ]);

    $customer1 = User::factory()->raw([
        'role' => 'customer',
        'name' => 'new name',
    ]);

    $response = putJson('api/v1/customers/'.$customer->id, $customer1);

    $this->assertDatabaseHas('users', [
        'id' => $customer->id,
        'name' => $customer1['name'],
        'email' => $customer1['email'],
        'role' => $customer1['role'],
        'company_id' => $customer1['company_id'],
    ]);

    $response
        ->assertOk()
        ->assertJson([
            'success' => true,
        ]);
});

test('update validates using a form request', function () {
    $this->assertActionUsesFormRequest(
        CustomersController::class,
        'update',
        CustomerRequest::class
    );
});

test('search customers', function () {
    $filters = [
        'page' => 1,
        'limit' => 15,
        'search' => 'doe',
        'email' => '.com',
    ];

    $queryString = http_build_query($filters, '', '&');

    $response = getJson('api/v1/customers?'.$queryString);

    $response->assertOk();
});

test('delete multiple customer', function () {
    $customers = User::factory()->count(4)->create([
        'role' => 'customer',
    ]);

    $ids = $customers->pluck('id');

    $data = [
        'ids' => $ids,
    ];

    $response = postJson('api/v1/customers/delete', $data);

    $response
        ->assertOk()
        ->assertJson([
            'success' => true,
        ]);
});
