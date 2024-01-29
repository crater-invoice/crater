<?php

namespace InvoiceShelf\Http\Controllers\V1\Installation;

use Illuminate\Http\Request;
use InvoiceShelf\Http\Controllers\Controller;

class FinishController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        \Storage::disk('local')->put('database_created', 'database_created');

        return response()->json(['success' => true]);
    }
}
