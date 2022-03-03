<?php

namespace Crater\Http\Controllers\V1\Admin\Settings;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\UpdateSettingsRequest;
use Crater\Models\Company;
use Crater\Models\CompanySetting;

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
        $company = Company::find($request->header('company'));
        $this->authorize('manage company', $company);

        $companyCurrency = CompanySetting::getSetting('currency', $request->header('company'));
        $data = $request->settings;

        if ($companyCurrency !== $data['currency'] && $company->hasTransactions()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update company currency after transactions are created.'
            ]);
        }

        CompanySetting::setSettings($data, $request->header('company'));

        return response()->json([
            'success' => true,
        ]);
    }
}
