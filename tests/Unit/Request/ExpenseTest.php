<?php

use Crater\Http\Requests\ExpenseRequest;

test('expense request validation rules', function () {
    $request = new ExpenseRequest();

    $this->assertEquals(
        [
            'expense_date' => [
                'required',
            ],
            'expense_category_id' => [
                'required',
            ],
            'exchange_rate' => [
                'nullable'
            ],
            'payment_method_id' => [
                'nullable',
            ],
            'amount' => [
                'required',
            ],
            'customer_id' => [
                'nullable',
            ],
            'notes' => [
                'nullable',
            ],
            'currency_id' => [
                'required'
            ],
            'attachment_receipt' => [
                'nullable',
                'file',
                'mimes:jpg,png,pdf,doc,docx,xls,xlsx,ppt,pptx',
                'max:20000'
            ]
        ],
        $request->rules()
    );
});

test('expense request authorize', function () {
    $request = new ExpenseRequest();

    $this->assertTrue($request->authorize());
});
