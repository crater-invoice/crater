<?php

use Crater\Http\Requests\PaymentMethodRequest;
use Illuminate\Validation\Rule;

test('payment method request validation rules', function () {
    $request = new PaymentMethodRequest();

    $this->assertEquals(
        [
            'name' => [
                'required',
                Rule::unique('units')
                    ->where('payment_methods', $request->header('company')),
            ],
        ],
        $request->rules()
    );
});

test('payment method request authorize', function () {
    $request = new PaymentMethodRequest();

    $this->assertTrue($request->authorize());
});
