<?php

use Crater\Http\Requests\BulkExchangeRateRequest;

test('bulk exchange rate request validation rules', function () {
    $request = new BulkExchangeRateRequest();

    $this->assertEquals(
        [
            'currencies' => [
                'required'
            ],
            'currencies.*.id' => [
                'required',
                'numeric'
            ],
            'currencies.*.exchange_rate' => [
                'required'
            ],
        ],
        $request->rules()
    );
});

test('bulk exchange rate request authorize', function () {
    $request = new BulkExchangeRateRequest();

    $this->assertTrue($request->authorize());
});
