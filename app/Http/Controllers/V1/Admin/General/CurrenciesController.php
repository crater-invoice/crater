<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\General;

use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Http\Resources\CurrencyResource;
use InvoiceShelf\Models\Currency;
use Illuminate\Http\Request;

class CurrenciesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $currencies = Currency::latest()->get();

        return CurrencyResource::collection($currencies);
    }
}
