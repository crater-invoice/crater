<?php

namespace InvoiceShelf\Http\Controllers\V1\Admin\Config;

use Illuminate\Http\Request;
use InvoiceShelf\Http\Controllers\Controller;

class FiscalYearsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return response()->json([
            'fiscal_years' => config('invoiceshelf.fiscal_years'),
        ]);
    }
}
