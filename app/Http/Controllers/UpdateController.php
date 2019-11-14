<?php

namespace Laraspace\Http\Controllers;

use Illuminate\Http\Request;
use Laraspace\Space\Updater;
use Laraspace\Space\SiteApi;

class UpdateController extends Controller
{
    public function update(Request $request)
    {
        set_time_limit(600); // 10 minutes

        $json = Updater::update($request->installed, $request->version);

        return response()->json($json);
    }

    public function checkLatestVersion(Request $request)
    {
        set_time_limit(600); // 10 minutes

        $json = Updater::checkForUpdate();

        return response()->json($json);
    }
}
