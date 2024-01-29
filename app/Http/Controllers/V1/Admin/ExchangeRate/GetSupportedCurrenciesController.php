<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\ExchangeRate;

use Illuminate\Http\Request;
use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Models\ExchangeRateProvider;
use InvoiceShelf\Traits\ExchangeRateProvidersTrait;

class GetSupportedCurrenciesController extends Controller
{
    use ExchangeRateProvidersTrait;

    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $this->authorize('viewAny', ExchangeRateProvider::class);

        return $this->getSupportedCurrencies($request);
    }
}
