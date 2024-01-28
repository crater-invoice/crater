<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\Update;

use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Models\Setting;
use InvoiceShelf\Space\Updater;
use Illuminate\Http\Request;

class CheckVersionController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        if ((! $request->user()) || (! $request->user()->isOwner())) {
            return response()->json([
                'success' => false,
                'message' => 'You are not allowed to update this app.'
            ], 401);
        }

        set_time_limit(600); // 10 minutes

        $json = Updater::checkForUpdate(Setting::getSetting('version'));

        return response()->json($json);
    }
}
