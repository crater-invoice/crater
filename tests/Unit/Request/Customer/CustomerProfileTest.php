<?php

use Crater\Http\Requests\Customer\CustomerProfileRequest;
use Illuminate\Validation\Rule;

test('customer profile request validation rules', function () {
    $request = new CustomerProfileRequest();

    $this->assertEquals(
        [
            'name' => [
                'nullable',
            ],
            'password' => [
                'nullable',
                'min:8',
            ],
            'email' => [
                'nullable',
                'email',
                Rule::unique('customers')->where('company_id', $request->header('company'))->ignore(Auth::id(), 'id')
            ],
            'billing.name' => [
                'nullable',
            ],
            'billing.address_street_1' => [
                'nullable',
            ],
            'billing.address_street_2' => [
                'nullable',
            ],
            'billing.city' => [
                'nullable',
            ],
            'billing.state' => [
                'nullable',
            ],
            'billing.country_id' => [
                'nullable',
            ],
            'billing.zip' => [
                'nullable',
            ],
            'billing.phone' => [
                'nullable',
            ],
            'billing.fax' => [
                'nullable',
            ],
            'shipping.name' => [
                'nullable',
            ],
            'shipping.address_street_1' => [
                'nullable',
            ],
            'shipping.address_street_2' => [
                'nullable',
            ],
            'shipping.city' => [
                'nullable',
            ],
            'shipping.state' => [
                'nullable',
            ],
            'shipping.country_id' => [
                'nullable',
            ],
            'shipping.zip' => [
                'nullable',
            ],
            'shipping.phone' => [
                'nullable',
            ],
            'shipping.fax' => [
                'nullable',
            ]
        ],
        $request->rules()
    );
});

test('customer profile request authorize', function () {
    $request = new CustomerProfileRequest();

    $this->assertTrue($request->authorize());
});
