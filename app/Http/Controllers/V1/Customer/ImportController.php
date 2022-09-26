<?php

namespace Crater\Http\Controllers\V1\Customer;

use Crater\Http\Controllers\Controller;
use Crater\Imports\CustomerImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function import(Request $request)
    {
        Excel::import(new CustomerImport($request->user()->id, $request->header('company')), $request->file('import_upload'));

        return response()->json([
            'success' => true,
            'data' => [],
        ]);
    }
}
