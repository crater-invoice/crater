<?php

use Crater\Http\Requests\PaymentMethodRequest;

test('payment method request validation rules', function () {
    $request = new PaymentMethodRequest();

    $this->assertEquals(
        [
            'name' => [
                'required',
                'unique:payment_methods,name',
            ],
        ],
        $request->rules()
    );
});

test('payment method request authorize', function () {
    $request = new PaymentMethodRequest();

    $this->assertTrue($request->authorize());
});
