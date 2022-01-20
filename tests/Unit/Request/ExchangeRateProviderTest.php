<?php

use Crater\Http\Requests\ExchangeRateProviderRequest;

test('exchange rate provider request validation rules', function () {
    $request = new ExchangeRateProviderRequest();

    $this->assertEquals(
        [
            'driver' => [
                'required'
            ],
            'key' => [
                'required',
            ],
            'currencies' => [
                'nullable',
            ],
            'currencies.*' => [
                'nullable',
            ],
            'driver_config' => [
                'nullable'
            ],
            'active' => [
                'nullable',
                'boolean'
            ],
        ],
        $request->rules()
    );
});

test('exchange rate provider request authorize', function () {
    $request = new ExchangeRateProviderRequest();

    $this->assertTrue($request->authorize());
});
