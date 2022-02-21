<?php

use Crater\Models\CustomField;
use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);
});

test('custom field belongs to company', function () {
    $customField = CustomField::factory()->create();

    $this->assertTrue($customField->company()->exists());
});

test('custom field has many custom field value', function () {
    $customField = CustomField::factory()->hasCustomFieldValues(5)->create();

    $this->assertCount(5, $customField->customFieldValues);

    $this->assertTrue($customField->customFieldValues()->exists());
});
