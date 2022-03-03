<?php

namespace Crater\Http\Controllers\V1\Admin\Settings;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\UpdateSettingsRequest;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\Currency;

class UpdateCompanySettingsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\UpdateSettingsRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(UpdateSettingsRequest $request)
    {
        $this->authorize('manage company', Company::find($request->header('company')));

        $companyCurrency = CompanySetting::getSetting('currency', $request->header('company'));

        $data = $request->settings;
        $currency = Currency::find((int)$companyCurrency);

        if ($companyCurrency !== $data['currency'] && $currency->checkTransactions()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot change currency once transaction is created.'
            ]);
        }

        CompanySetting::setSettings($data, $request->header('company'));

        return response()->json([
            'success' => true,
        ]);
    }
}
