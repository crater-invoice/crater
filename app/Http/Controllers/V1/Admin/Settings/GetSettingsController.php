<?php

namespace Crater\Http\Controllers\V1\Admin\Settings;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\GetSettingRequest;
use Crater\Models\Setting;
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
