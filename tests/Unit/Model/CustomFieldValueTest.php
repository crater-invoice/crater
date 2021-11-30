<?php

use Crater\Models\CustomFieldValue;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);
});

test('custom field value belongs to company', function () {
    $fieldValue = CustomFieldValue::factory()->create();

    $this->assertTrue($fieldValue->company()->exists());
});

test('custom field value belongs to custom field', function () {
    $fieldValue = CustomFieldValue::factory()->forCustomField()->create();

    $this->assertTrue($fieldValue->customField()->exists());
});
