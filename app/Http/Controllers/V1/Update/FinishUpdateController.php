<?php

namespace Crater\Http\Controllers\V1\Update;

use Crater\Http\Controllers\Controller;
use Crater\Space\Updater;
use Illuminate\Http\Request;

class FinishUpdateController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'installed' => 'required',
            'version' => 'required',
        ]);

        $json = Updater::finishUpdate($request->installed, $request->version);

        return response()->json($json);
    }
}
