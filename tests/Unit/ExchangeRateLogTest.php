<?php

use Crater\Models\ExchangeRateLog;
use Crater\Models\Expense;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);
});

test('an exchange rate log belongs to company', function () {
    $exchangeRateLog = ExchangeRateLog::factory()->forCompany()->create();

    $this->assertTrue($exchangeRateLog->company->exists());
});

test('add exchange rate log', function () {
    $expense = Expense::factory()->create();
    $response = ExchangeRateLog::addExchangeRateLog($expense);

    $this->assertDatabaseHas('exchange_Rate_logs', [
        'exchange_rate' => $response->exchange_rate,
        'base_currency_id' => $response->base_currency_id,
        'currency_id' => $response->currency_id,
    ]);
});
