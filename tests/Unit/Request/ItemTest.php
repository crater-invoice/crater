<?php

use Crater\Http\Requests\ItemsRequest;

test('items request validation rules', function () {
    $request = new ItemsRequest();

    $this->assertEquals(
        [
            'name' => [
                'required',
            ],
            'price' => [
                'required',
            ],
            'unit_id' => [
                'nullable',
            ],
            'description' => [
                'nullable',
            ],
        ],
        $request->rules()
    );
});

test('item request authorize', function () {
    $request = new ItemsRequest();

    $this->assertTrue($request->authorize());
});
