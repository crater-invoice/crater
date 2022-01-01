<?php

namespace Crater\Http\Controllers\V1\Admin\ExchangeRate;

use Crater\Http\Controllers\Controller;
use Crater\Models\Currency;
use Crater\Models\ExchangeRateProvider;
use Illuminate\Http\Request;

class GetActiveProviderController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Currency $currency)
    {
        $query = ExchangeRateProvider::whereCompany()->whereJsonContains('currencies', $currency->code)
                ->where('active', true)
                ->get();

        if (count($query) !== 0) {
            return response()->json([
                'success' => true,
                'message' => 'provider_active',
            ], 200);
        }

        return response()->json([
            'error' => 'no_active_provider',
        ], 200);
    }
}
