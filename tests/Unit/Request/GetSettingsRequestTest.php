<?php

use Crater\Http\Requests\GetSettingsRequest;

test('get settings request rules', function () {
    $request = new GetSettingsRequest();

    $this->assertEquals(
        [
            'settings' => [
                'required',
            ],
            'settings.*' => [
                'required',
                'string',
            ],
        ],
        $request->rules()
    );
});


test('get settings request authorize', function () {
    $request = new GetSettingsRequest();

    $this->assertTrue($request->authorize());
});
