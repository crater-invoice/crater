<?php

use Crater\Http\Requests\UserRequest;
use Illuminate\Validation\Rule;

test('user request validation rules', function () {
    $request = new UserRequest();

    $this->assertEquals(
        [
            'name' => [
                'required',
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users'),
            ],
            'phone' => [
                'nullable',
            ],
            'password' => [
                'required',
                'min:8',
            ],
            'companies' => [
                'required',
            ],
            'companies.*.id' => [
                'required',
            ],
            'companies.*.role' => [
                'required',
            ],
        ],
        $request->rules()
    );
});

test('user request authorize', function () {
    $request = new UserRequest();

    $this->assertTrue($request->authorize());
});
