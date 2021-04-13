<?php

use Crater\Http\Controllers\V1\Users\UsersController;
use Crater\Http\Requests\UserRequest;
use Crater\Models\User;
use Laravel\Sanctum\Sanctum;
use function Pest\Faker\faker;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);

    $user = User::where('role', 'super admin')->first();

    $this->withHeaders([
        'company' => $user->company_id,
    ]);

    Sanctum::actingAs(
        $user,
        ['*']
    );
});

getJson('/api/v1/users')->assertOk();

test('store user using a form request', function () {
    $this->assertActionUsesFormRequest(
        UsersController::class,
        'store',
        UserRequest::class
    );
});

// test('store user', function () {
//     $data = [
//         'name' => faker()->name,
//         'email' => faker()->unique()->safeEmail,
//         'phone' => faker()->phoneNumber,
//         'password' => faker()->password
//     ];

//     postJson('/api/v1/users', $data)->assertOk();

//     $this->assertDatabaseHas('users', [
//         'name' => $data['name'],
//         'email' => $data['email'],
//         'phone' => $data['phone'],
//     ]);
// });

test('get user', function () {
    $user = User::factory()->create();

    getJson("/api/v1/users/{$user->id}")->assertOk();
});

test('update user using a form request', function () {
    $this->assertActionUsesFormRequest(
        UsersController::class,
        'update',
        UserRequest::class
    );
});

// test('update user', function () {
//     $user = User::factory()->create();

//     $data = [
//         'name' => faker()->name,
//         'email' => faker()->unique()->safeEmail,
//         'phone' => faker()->phoneNumber,
//         'password' => faker()->password
//     ];

//     putJson("/api/v1/users/{$user->id}", $data)->assertOk();

//     $this->assertDatabaseHas('users', [
//         'name' => $data['name'],
//         'email' => $data['email'],
//         'phone' => $data['phone'],
//     ]);
// });

test('delete users', function () {
    $user = User::factory()->create();
    $data['users'] = [$user->id];

    postJson("/api/v1/users/delete", $data)->assertOk();

    $this->assertDeleted($user);
});
