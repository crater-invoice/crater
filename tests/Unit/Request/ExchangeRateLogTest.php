<?php

use Crater\Http\Requests\ExchangeRateLogRequest;

test('exchange rate log request validation rules', function () {
    $request = new ExchangeRateLogRequest();

    $this->assertEquals(
        [
            'exchange_rate' => [
                'required',
            ],
            'currency_id' => [
                'required'
            ],
        ],
        $request->rules()
    );
});

test('exchange rate log request authorize', function () {
    $request = new ExchangeRateLogRequest();

    $this->assertTrue($request->authorize());
});
