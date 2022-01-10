<?php

use Crater\Http\Requests\CompanyRequest;
use Illuminate\Validation\Rule;

test('company request validation rules', function () {
    $request = new CompanyRequest();

    $this->assertEquals(
        [
            'name' => [
                'required',
                Rule::unique('companies')->ignore($request->header('company'), 'id'),
            ],
            'slug' => [
                'nullable'
            ],
            'address.country_id' => [
                'required',
            ],
        ],
        $request->rules()
    );
});

test('company request authorize', function () {
    $request = new CompanyRequest();

    $this->assertTrue($request->authorize());
});
