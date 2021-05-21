<?php

use Crater\Http\Requests\UpdateSettingsRequest;

test('update settings request rules', function () {
    $request = new UpdateSettingsRequest();

    $this->assertEquals(
        [
            'settings' => [
                'required',
            ],
            'settings.*' => [
                'required',
            ],
        ],
        $request->rules()
    );
});


test('update settings request authorize', function () {
    $request = new UpdateSettingsRequest();

    $this->assertTrue($request->authorize());
});
