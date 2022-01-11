<?php

use Crater\Http\Requests\ProfileRequest;
use Illuminate\Validation\Rule;

test('profile request validation rules', function () {
    $request = new ProfileRequest();

    $this->assertEquals(
        [
            'name' => [
                'required',
            ],
            'password' => [
                'nullable',
                'min:8',
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore(Auth::id(), 'id'),
            ],
        ],
        $request->rules()
    );
});

test('profile request authorize', function () {
    $request = new ProfileRequest();

    $this->assertTrue($request->authorize());
});
