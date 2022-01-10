<?php

use Crater\Http\Requests\LoginRequest;

test('login request validation rules', function () {
    $request = new LoginRequest();

    $this->assertEquals(
        [
            'username' => [
                'required',
            ],
            'password' => [
                'required',
            ],
            'device_name' => [
                'required'
            ],
        ],
        $request->rules()
    );
});

test('login request authorize', function () {
    $request = new LoginRequest();

    $this->assertTrue($request->authorize());
});
