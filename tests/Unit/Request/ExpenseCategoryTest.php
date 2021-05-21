<?php

use Crater\Http\Requests\ExpenseCategoryRequest;

test('expense category request validation rules', function () {
    $request = new ExpenseCategoryRequest();

    $this->assertEquals(
        [
            'name' => [
                'required',
            ],
            'description' => [
                'nullable',
            ],
        ],
        $request->rules()
    );
});

test('expense category request authorize', function () {
    $request = new ExpenseCategoryRequest();

    $this->assertTrue($request->authorize());
});
