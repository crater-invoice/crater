<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\ExchangeRate;

use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Models\ExchangeRateProvider;
use InvoiceShelf\Traits\ExchangeRateProvidersTrait;
use Illuminate\Http\Request;

class GetSupportedCurrenciesController extends Controller
{
    use ExchangeRateProvidersTrait;

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $this->authorize('viewAny', ExchangeRateProvider::class);

        return $this->getSupportedCurrencies($request);
    }
}
