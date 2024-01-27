<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\General;

use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Models\Currency;
use InvoiceShelf\Models\Estimate;
use InvoiceShelf\Models\Invoice;
use InvoiceShelf\Models\Payment;
use InvoiceShelf\Models\Tax;
use Illuminate\Http\Request;

class GetAllUsedCurrenciesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $invoices = Invoice::where('exchange_rate', null)->pluck('currency_id')->toArray();

        $taxes = Tax::where('exchange_rate', null)->pluck('currency_id')->toArray();

        $estimates = Estimate::where('exchange_rate', null)->pluck('currency_id')->toArray();

        $payments = Payment::where('exchange_rate', null)->pluck('currency_id')->toArray();

        $currencies = array_merge($invoices, $taxes, $estimates, $payments);

        return response()->json([
            'currencies' => Currency::whereIn('id', $currencies)->get()
        ]);
    }
}
