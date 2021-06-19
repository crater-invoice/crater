<?php

use Crater\Http\Requests\EstimatesRequest;
use Crater\Models\Estimate;
use Crater\Rules\UniqueNumber;

test('estimate request validation rules', function () {
    $request = new EstimatesRequest();

    $this->assertEquals(
        [
            'estimate_date' => [
                'required',
            ],
            'expiry_date' => [
                'required',
            ],
            'user_id' => [
                'required',
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
                'max:255',
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
            'estimate_number' => [
                'required',
                new UniqueNumber(Estimate::class),
            ],
        ],
        $request->rules()
    );
});

test('estimate request authorize', function () {
    $request = new EstimatesRequest();

    $this->assertTrue($request->authorize());
});
