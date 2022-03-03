<?php

namespace Crater\Http\Controllers\V1\Admin\Settings;

use Crater\Http\Controllers\Controller;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\Currency;
use Illuminate\Http\Request;

class CompanyCurrencyTransactionController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $this->authorize('manage company', Company::find($request->header('company')));

        $companyCurrency = CompanySetting::getSetting('currency', $request->header('company'));

        $currency = Currency::find((int)$companyCurrency);

        if (
            $currency->customers()->exists() ||
            $currency->items()->exists() ||
            $currency->invoices()->exists() ||
            $currency->estimates()->exists() ||
            $currency->expenses()->exists() ||
            $currency->payments()->exists() ||
            $currency->recurringInvoices()->exists()
        ) {
            return response()->json([
                'success' => false,
            ]);
        }

        return response()->json([
            'success' => true,
        ]);
    }
}
