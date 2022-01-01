<?php

use Crater\Http\Requests\EstimatesRequest;
use Illuminate\Validation\Rule;

test('estimate request validation rules', function () {
    $request = new EstimatesRequest();

    $this->assertEquals(
        [
            'estimate_date' => [
                'required',
            ],
            'expiry_date' => [
                'nullable',
            ],
            'customer_id' => [
                'required',
            ],
            'estimate_number' => [
                'required',
                Rule::unique('estimates')->where('company_id', $request->header('company'))
            ],
            'exchange_rate' => [
                'nullable'
            ],
            'discount' => [
                'required',
            ],
            'discount_val' => [
                'required',
            ],
            'sub_total' => [
                'required',
            ],
            'total' => [
                'required',
            ],
            'tax' => [
                'required',
            ],
            'template_name' => [
                'required'
            ],
            'items' => [
                'required',
                'array',
            ],
            'items.*.description' => [
                'nullable',
            ],
            'items.*' => [
                'required',
                'max:255',
            ],
            'items.*.name' => [
                'required',
            ],
            'items.*.quantity' => [
                'required',
            ],
            'items.*.price' => [
                'required',
            ],
        ],
        $request->rules()
    );
});

test('estimate request authorize', function () {
    $request = new EstimatesRequest();

    $this->assertTrue($request->authorize());
});
