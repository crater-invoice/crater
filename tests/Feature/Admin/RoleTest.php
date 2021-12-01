<?php

use Crater\Models\User;
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
        "password" => "lorem@123"
    ];
    $data['companies'] = [
        [
            "role" => "super admin",
            "id" => 1
        ]
    ];

    postJson('api/v1/users', $data)
        ->assertStatus(201);

    $data = collect($data)
        ->only([
            'email',
            'name',
        ])
        ->toArray();

    $this->assertDatabaseHas('users', $data);
});
