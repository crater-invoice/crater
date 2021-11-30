<?php

use Crater\Http\Requests\CompanyRequest;

test('company request validation rules', function () {
    $request = new CompanyRequest();

    $this->assertEquals(
        [
            'name' => [
                'required',
            ],
            'country_id' => [
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
