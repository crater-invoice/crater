<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\Settings;

use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Http\Requests\GetSettingsRequest;
use InvoiceShelf\Models\CompanySetting;

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
