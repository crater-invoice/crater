<?php

namespace Crater\Http\Controllers\V1\Admin\ExchangeRate;

use Crater\Http\Controllers\Controller;
use Crater\Models\ExchangeRateProvider;
use Illuminate\Http\Request;

class GetUsedCurrenciesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $this->authorize('viewAny', ExchangeRateProvider::class);

        $providerId = $request->provider_id;

        $activeExchangeRateProviders = ExchangeRateProvider::where('active', true)
            ->whereCompany()
            ->when($providerId, function ($query) use ($providerId) {
                return $query->where('id', '<>', $providerId);
            })
            ->pluck('currencies');
        $activeExchangeRateProvider = [];

        foreach ($activeExchangeRateProviders as $data) {
            if (is_array($data)) {
                for ($limit = 0; $limit < count($data); $limit++) {
                    $activeExchangeRateProvider[] = $data[$limit];
                }
            }
        }

        $allExchangeRateProviders = ExchangeRateProvider::whereCompany()->pluck('currencies');
        $allExchangeRateProvider = [];

        foreach ($allExchangeRateProviders as $data) {
            if (is_array($data)) {
                for ($limit = 0; $limit < count($data); $limit++) {
                    $allExchangeRateProvider[] = $data[$limit];
                }
            }
        }

        return response()->json([
            'allUsedCurrencies' => $allExchangeRateProvider ? $allExchangeRateProvider : [],
            'activeUsedCurrencies' => $activeExchangeRateProvider ? $activeExchangeRateProvider : [],
        ]);
    }
}
