<?php

use Crater\Http\Requests\UnitRequest;
use Illuminate\Validation\Rule;

test('unit request validation rules', function () {
    $request = new UnitRequest();

    $this->assertEquals(
        [
            'name' => [
                'required',
                Rule::unique('units')
                    ->where('company_id', $request->header('company')),
            ],
        ],
        $request->rules()
    );
});

test('unit request authorize', function () {
    $request = new UnitRequest();

    $this->assertTrue($request->authorize());
});
