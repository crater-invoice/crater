<?php

namespace InvoiceShelf\Http\Controllers;

use Illuminate\Http\Request;
use InvoiceShelf\Models\Setting;

class AppVersionController extends Controller
{
    /**
     * Handle the incoming request.
     *
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
