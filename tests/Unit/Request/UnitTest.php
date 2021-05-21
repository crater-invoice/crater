<?php

use Crater\Http\Requests\UnitRequest;

test('unit request validation rules', function () {
    $request = new UnitRequest();

    $this->assertEquals(
        [
            'name' => [
                'required',
                'unique:units,name',
            ],
        ],
        $request->rules()
    );
});

test('unit request authorize', function () {
    $request = new UnitRequest();

    $this->assertTrue($request->authorize());
});
