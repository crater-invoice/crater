<?php

use Crater\Http\Requests\CompaniesRequest;
use Illuminate\Validation\Rule;

test('companies request validation rules', function () {
    $request = new CompaniesRequest();

    $this->assertEquals(
        [
            'name' => [
                'required',
                Rule::unique('companies'),
                'string'
            ],
            'currency' => [
                'required'
            ],
            'address.name' => [
                'nullable',
            ],
            'address.address_street_1' => [
                'nullable',
            ],
            'address.address_street_2' => [
                'nullable',
            ],
            'address.city' => [
                'nullable',
            ],
            'address.state' => [
                'nullable',
            ],
            'address.country_id' => [
                'required',
            ],
            'address.zip' => [
                'nullable',
            ],
            'address.phone' => [
                'nullable',
            ],
            'address.fax' => [
                'nullable',
            ],
        ],
        $request->rules()
    );
});

test('companies request authorize', function () {
    $request = new CompaniesRequest();

    $this->assertTrue($request->authorize());
});
