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
        $this->authorize('manage company', Company::find($request->header('company')));

        CompanySetting::setSettings($request->settings, $request->header('company'));

        return response()->json([
            'success' => true,
        ]);
    }
}
