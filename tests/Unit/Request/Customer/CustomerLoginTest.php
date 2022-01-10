<?php

use Crater\Http\Requests\Customer\CustomerLoginRequest;

test('customer login request validation rules', function () {
    $request = new CustomerLoginRequest();

    $this->assertEquals(
        [
            'email' => [
                'required',
                'string'
            ],
            'password' => [
                'required',
                'string'
            ],
        ],
        $request->rules()
    );
});

test('customer login request authorize', function () {
    $request = new CustomerLoginRequest();

    $this->assertTrue($request->authorize());
});
