<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\Settings;

use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Http\Requests\GetSettingRequest;
use InvoiceShelf\Models\Setting;
use Illuminate\Http\Request;

class GetSettingsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(GetSettingRequest $request)
    {
        $this->authorize('manage settings');

        $setting = Setting::getSetting($request->key);

        return response()->json([
            $request->key => $setting
        ]);
    }
}
