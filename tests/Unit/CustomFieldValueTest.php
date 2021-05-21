<?php

use Crater\Models\CustomFieldValue;
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

test('custom field value belongs to company', function () {
    $fieldValue = CustomFieldValue::factory()->create();

    $this->assertTrue($fieldValue->company()->exists());
});

test('custom field value belongs to custom field', function () {
    $fieldValue = CustomFieldValue::factory()->forCustomField()->create();

    $this->assertTrue($fieldValue->customField()->exists());
});
