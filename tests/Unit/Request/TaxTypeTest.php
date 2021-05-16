<?php

use Crater\Http\Requests\TaxTypeRequest;

test('tax type request validation rules', function () {
    $request = new TaxTypeRequest();

    $this->assertEquals(
        [
            'name' => [
                'required',
            ],
            'percent' => [
                'required',
            ],
            'description' => [
                'nullable',
            ],
            'compound_tax' => [
                'nullable',
            ],
            'collective_tax' => [
                'nullable',
            ],
        ],
        $request->rules()
    );
});


test('tax type request authorize', function () {
    $request = new TaxTypeRequest();

    $this->assertTrue($request->authorize());
});
