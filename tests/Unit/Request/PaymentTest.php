<?php

use Crater\Http\Requests\PaymentRequest;
use Illuminate\Validation\Rule;

test('payment request validation rules', function () {
    $request = new PaymentRequest();

    $this->assertEquals(
        [
            'payment_date' => [
                'required',
            ],
            'customer_id' => [
                'required',
            ],
            'amount' => [
                'required',
            ],
            'payment_number' => [
                'required',
                Rule::unique('payments')->where('company_id', null)
            ],
            'invoice_id' => [
                'nullable',
            ],
            'payment_method_id' => [
                'nullable',
            ],
            'notes' => [
                'nullable',
            ],
        ],
        $request->rules()
    );
});

test('payment request authorize', function () {
    $request = new PaymentRequest();

    $this->assertTrue($request->authorize());
});
