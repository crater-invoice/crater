<?php

namespace Laraspace\Http\Controllers;

use Illuminate\Http\Request;
use Laraspace\Setting;

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
