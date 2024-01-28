<?php

namespace InvoiceShelf\Http\Controllers;

use InvoiceShelf\Models\Setting;
use Illuminate\Http\Request;

class AppVersionController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $version = Setting::getSetting('version');

        return response()->json([
            'version' => $version,
        ]);
    }
}
