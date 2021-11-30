<?php

use Crater\Http\Requests\RecurringInvoiceRequest;

test('recurring invoice request validation rules', function () {
    $request = new RecurringInvoiceRequest();

    $this->assertEquals(
        [
            'starts_at' => [
                'required'
            ],
            'send_automatically' => [
                'required',
                'boolean'
            ],
            'customer_id' => [
                'required'
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
            'status' => [
                'required'
            ],
            'exchange_rate' => [
                'nullable'
            ],
            'frequency' => [
                'required'
            ],
            'limit_by' => [
                'required'
            ],
            'limit_count' => [
                'required_if:limit_by,COUNT',
            ],
            'limit_date' => [
                'required_if:limit_by,DATE',
            ],
            'items' => [
                'required'
            ],
            'items.*' => [
                'required'
            ]
        ],
        $request->rules()
    );
});

test('recurring invoice request authorize', function () {
    $request = new RecurringInvoiceRequest();

    $this->assertTrue($request->authorize());
});
