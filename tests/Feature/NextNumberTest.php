<?php

use Crater\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\{getJson};

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

test('next number', function () {
    $key = 'invoice';

    $response = getJson('api/v1/next-number?key='.$key);

    $response->assertStatus(200)->assertJson([
        'nextNumber' => '000001',
    ]);

    $key = 'estimate';

    $response = getJson('api/v1/next-number?key='.$key);

    $response->assertStatus(200)->assertJson([
        'nextNumber' => '000001',
    ]);

    $key = 'payment';

    $response = getJson('api/v1/next-number?key='.$key);

    $response->assertStatus(200)->assertJson([
        'nextNumber' => '000001',
    ]);
});
