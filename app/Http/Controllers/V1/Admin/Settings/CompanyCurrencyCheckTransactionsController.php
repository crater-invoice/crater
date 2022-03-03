<?php

namespace Crater\Http\Controllers\V1\Admin\Settings;

use Crater\Http\Controllers\Controller;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\Currency;
use Illuminate\Http\Request;

class CompanyCurrencyCheckTransactionsController extends Controller
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

        if ($currency->checkTransactions()) {
            return response()->json([
                'success' => false,
            ]);
        }

        return response()->json([
            'success' => true,
        ]);
    }
}
