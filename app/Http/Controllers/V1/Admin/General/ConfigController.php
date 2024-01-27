<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\General;

use InvoiceShelf\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return response()->json([
            $request->key => config('crater.'.$request->key),
        ]);
    }
}
