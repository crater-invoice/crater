<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\ExchangeRate;

use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Http\Requests\ExchangeRateProviderRequest;
use InvoiceShelf\Http\Resources\ExchangeRateProviderResource;
use InvoiceShelf\Models\ExchangeRateProvider;
use Illuminate\Http\Request;

class ExchangeRateProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', ExchangeRateProvider::class);

        $limit = $request->has('limit') ? $request->limit : 5;

        $exchangeRateProviders = ExchangeRateProvider::whereCompany()->paginate($limit);

        return ExchangeRateProviderResource::collection($exchangeRateProviders);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ExchangeRateProviderRequest $request)
    {
        $this->authorize('create', ExchangeRateProvider::class);

        $query = ExchangeRateProvider::checkActiveCurrencies($request);

        if (count($query) !== 0) {
            return respondJson('currency_used', 'Currency used.');
        }

        $checkConverterApi = ExchangeRateProvider::checkExchangeRateProviderStatus($request);

        if ($checkConverterApi->status() == 200) {
            $exchangeRateProvider = ExchangeRateProvider::createFromRequest($request);

            return new ExchangeRateProviderResource($exchangeRateProvider);
        }

        return $checkConverterApi;
    }

    /**
     * Display the specified resource.
     *
     * @param  \InvoiceShelf\Models\ExchangeRateProvider  $exchangeRateProvider
     * @return \Illuminate\Http\Response
     */
    public function show(ExchangeRateProvider $exchangeRateProvider)
    {
        $this->authorize('view', $exchangeRateProvider);

        return new ExchangeRateProviderResource($exchangeRateProvider);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \InvoiceShelf\Models\ExchangeRateProvider  $exchangeRateProvider
     * @return \Illuminate\Http\Response
     */
    public function update(ExchangeRateProviderRequest $request, ExchangeRateProvider $exchangeRateProvider)
    {
        $this->authorize('update', $exchangeRateProvider);

        $query = $exchangeRateProvider->checkUpdateActiveCurrencies($request);

        if (count($query) !== 0) {
            return respondJson('currency_used', 'Currency used.');
        }

        $checkConverterApi = ExchangeRateProvider::checkExchangeRateProviderStatus($request);

        if ($checkConverterApi->status() == 200) {
            $exchangeRateProvider->updateFromRequest($request);

            return new ExchangeRateProviderResource($exchangeRateProvider);
        }

        return $checkConverterApi;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \InvoiceShelf\Models\ExchangeRateProvider  $exchangeRateProvider
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExchangeRateProvider $exchangeRateProvider)
    {
        $this->authorize('delete', $exchangeRateProvider);

        if ($exchangeRateProvider->active == true) {
            return respondJson('provider_active', 'Provider Active.');
        }

        $exchangeRateProvider->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
