<?php

use Crater\Http\Requests\CustomerRequest;

test('customer request post validation rules', function () {
    $request = new CustomerRequest();

    $request->setMethod('POST');

    $this->assertEquals(
        [
            'name' => [
                'required',
            ],
            'addresses.*.address_street_1' => [
                'max:255',
            ],
            'addresses.*.address_street_2' => [
                'max:255',
            ],
            'email' => [
                'email',
                'nullable',
                'unique:users,email',
            ],
        ],
        $request->rules()
    );
});

test('customer request put validation rules', function () {
    $request = new CustomerRequest();

    $request->setMethod('PUT');

    $this->assertEquals(
        [
                'name' => [
                'required',
            ],
            'addresses.*.address_street_1' => [
                'max:255',
            ],
            'addresses.*.address_street_2' => [
                'max:255',
            ],
            'email' => [
                'email',
                'nullable',
                'unique:users,email',
            ],
        ],
        $request->rules()
    );
});

test('customer request authorize', function () {
    $request = new CustomerRequest();

    $this->assertTrue($request->authorize());
});
