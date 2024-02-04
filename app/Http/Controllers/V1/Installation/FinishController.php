<?php

namespace InvoiceShelf\Http\Controllers\V1\Installation;

use Illuminate\Http\Request;
use InvoiceShelf\Http\Controllers\Controller;
use InvoiceShelf\Space\InstallUtils;

class FinishController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        InstallUtils::createDbMarker();

        return response()->json(['success' => true]);
    }
}
