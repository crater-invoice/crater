<?php

use Crater\Http\Requests\TaxTypeRequest;
use Crater\Models\TaxType;
use Illuminate\Validation\Rule;

test('tax type request validation rules', function () {
    $request = new TaxTypeRequest();

    $this->assertEquals(
        [
            'name' => [
                'required',
                Rule::unique('tax_types')
                ->where('type', TaxType::TYPE_GENERAL)
                ->where('company_id', $request->header('company'))
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
