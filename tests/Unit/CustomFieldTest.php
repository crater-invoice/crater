<?php

use Crater\Models\CustomField;
use Crater\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;

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

test('custom field belongs to company', function () {
    $customField = CustomField::factory()->create();

    $this->assertTrue($customField->company()->exists());
});

test('custom field has many custom field value', function () {
    $customField = CustomField::factory()->hascustomFieldValue(5)->create();

    $this->assertCount(5, $customField->customFieldValue);

    $this->assertTrue($customField->customFieldValue()->exists());
});
