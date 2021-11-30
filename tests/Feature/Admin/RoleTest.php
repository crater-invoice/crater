<?php

use Crater\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\postJson;

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

test('create super admin role', function () {
    $data = [
        "email" => "loremipsum@gmail.com",
        "name" => "lorem",
        "role" => "super admin",
        "password" => "lorem@123"
    ];

    postJson('api/v1/users', $data)->assertStatus(201);

    $this->assertDatabaseHas('users', Arr::except($data, ['password']));
});
