<?php

use Crater\Http\Requests\CustomFieldRequest;

test('custom field request validation rules', function () {
    $request = new CustomFieldRequest();

    $this->assertEquals(
        [
            'name' => 'required',
            'label' => 'required',
            'model_type' => 'required',
            'type' => 'required',
            'is_required' => 'required|boolean',
            'options' => 'array',
            'placeholder' => 'string|nullable',
            'order' => 'required',
        ],
        $request->rules()
    );
});

test('custom field request authorize', function () {
    $request = new CustomFieldRequest();

    $this->assertTrue($request->authorize());
});
