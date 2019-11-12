<?php

namespace Laraspace\Http\Controllers;

use Illuminate\Http\Request;
use Laraspace\Space\Updater;

class UpdateController extends Controller
{
    public function update(Request $request)
    {
        set_time_limit(600); // 10 minutes

        $json = Updater::update($request->alias, $request->installed, $request->version);

        return response()->json($json);
    }
}
