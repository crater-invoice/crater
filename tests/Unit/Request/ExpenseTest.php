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
            'amount' => [
                'required',
            ],
            'user_id' => [
                'nullable',
            ],
            'notes' => [
                'nullable',
            ],
        ],
        $request->rules()
    );
});

test('expense request authorize', function () {
    $request = new ExpenseRequest();

    $this->assertTrue($request->authorize());
});
