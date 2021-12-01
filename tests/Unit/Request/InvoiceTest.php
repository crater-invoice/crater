<?php

use Crater\Http\Requests\InvoicesRequest;
use Illuminate\Validation\Rule;

test('invoice request validation rules', function () {
    $request = new InvoicesRequest();

    $this->assertEquals(
        [
            'invoice_date' => [
                'required',
            ],
            'due_date' => [
                'nullable',
            ],
            'customer_id' => [
                'required',
            ],
            'invoice_number' => [
                'required',
                Rule::unique('invoices')->where('company_id', $request->header('company'))
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
            'items.*' => [
                'required',
                'max:255',
            ],
            'items.*.description' => [
                'nullable',
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

test('invoices request authorize', function () {
    $request = new InvoicesRequest();

    $this->assertTrue($request->authorize());
});
