<?php

namespace Crater\Http\Controllers;

use Illuminate\Http\Request;
use Crater\Setting;

class SettingsController extends Controller
{
    public function getAppVersion(Request $request)
    {
        $version = Setting::getSetting('version', $request->header('company'));

        return response()->json([
            'version' => $version,
        ]);
    }

}
