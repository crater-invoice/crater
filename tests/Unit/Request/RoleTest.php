<?php

use Crater\Http\Requests\RoleRequest;
use Illuminate\Validation\Rule;

test('role request validation rules', function () {
    $request = new RoleRequest();

    $this->assertEquals(
        [
            'name' => [
                'required',
                'string',
                Rule::unique('roles')->where('scope', $request->header('company'))
            ],
            'abilities' => [
                'required'
            ],
            'abilities.*' => [
                'required'
            ],
        ],
        $request->rules()
    );
});

test('role request authorize', function () {
    $request = new RoleRequest();

    $this->assertTrue($request->authorize());
});
