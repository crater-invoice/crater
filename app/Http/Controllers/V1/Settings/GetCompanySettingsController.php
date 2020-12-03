<?php

namespace Crater\Http\Controllers\V1\Settings;

use Crater\Models\CompanySetting;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\GetSettingsRequest;

class GetCompanySettingsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(GetSettingsRequest $request)
    {
        $settings = CompanySetting::getSettings($request->settings, $request->header('company'));

        return response()->json($settings);
    }
}
