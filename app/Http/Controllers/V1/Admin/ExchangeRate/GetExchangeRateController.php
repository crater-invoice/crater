<?php

namespace Crater\Http\Controllers\V1\Admin\ExchangeRate;

use Crater\Http\Controllers\Controller;
use Crater\Models\CompanySetting;
use Crater\Models\Currency;
use Crater\Models\ExchangeRateLog;
use Crater\Models\ExchangeRateProvider;
use Crater\Traits\ExchangeRateProvidersTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class GetExchangeRateController extends Controller
{
    use ExchangeRateProvidersTrait;

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Currency $currency)
    {
        $settings = CompanySetting::getSettings(['currency'], $request->header('company'));
        $baseCurrency = Currency::findOrFail($settings['currency']);

        $query = ExchangeRateProvider::whereJsonContains('currencies', $currency->code)
                ->where('active', true)
                ->get()
                ->toArray();

        $exchange_rate = ExchangeRateLog::where('base_currency_id', $currency->id)
                ->where('currency_id', $baseCurrency->id)
                ->orderBy('created_at', 'desc')
                ->value('exchange_rate');

        if ($query) {
            $filter = Arr::only($query[0], ['key', 'driver', 'driver_config']);
            $exchange_rate_value = $this->getExchangeRate($filter, $currency->code, $baseCurrency->code);

            if ($exchange_rate_value->status() == 200) {
                return $exchange_rate_value;
            }
        }
        if ($exchange_rate) {
            return response()->json([
                'exchangeRate' => [$exchange_rate],
            ], 200);
        }

        return response()->json([
            'error' => 'no_exchange_rate_available',
        ], 200);
    }
}
